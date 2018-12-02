<?php

namespace doyzheng\weixin\parking;

use doyzheng\weixin\base\Helper;
use doyzheng\weixin\base\BaseModule;

/**
 * 微信车主平台
 * Class Parking
 * @link    https://pay.weixin.qq.com/wiki/doc/api/pap_sl_jt.php?chapter=20_95
 * @package doyzheng\weixin\parking
 * @property Notify $notify
 */
class Module extends BaseModule
{
    
    /**
     * @var string 公众账号id
     */
    public $appid;
    
    /**
     * @var string 公众号签名秘钥
     */
    public $key;
    
    /**
     * @var string 商户号
     */
    public $mch_id;
    
    /**
     * @var string 子商户公众账号id
     */
    public $sub_appid;
    
    /**
     * @var string 子商户号
     */
    public $sub_mch_id;
    
    // 入场通知接口
    const API_NOTIFICATION = 'https://api.mch.weixin.qq.com/vehicle/pay/notification';
    // 查询用户状态
    const API_QUERY_STATE = 'https://api.mch.weixin.qq.com/vehicle/partnerpay/querystate';
    // 申请扣款
    const API_PAY_APPLY = 'https://api.mch.weixin.qq.com/vehicle/partnerpay/payapply';
    // 查询订单
    const API_QUERY_ORDER = 'https://api.mch.weixin.qq.com/vehicle/partnerpay/queryorder';
    // 下载对账单
    const API_DOWNLOAD_BILL = 'https://api.mch.weixin.qq.com/pay/downloadbill';
    
    /**
     * Module constructor.
     * @param $config
     */
    public function __construct($config)
    {
        if (empty($config['appid'])) {
            $this->app->exception->invalidArgument('公众账号不能为空: appid');
        }
        if (empty($config['key'])) {
            $this->app->exception->invalidArgument('商户平台密钥不能为空: key');
        }
        if (empty($config['mch_id'])) {
            $this->app->exception->invalidArgument('服务商商户号不能为空: mch_id');
        }
        if (empty($config['sub_app_id'])) {
            $this->app->exception->invalidArgument('子商户公众账号不能为空: sub_app_id');
        }
        if (empty($config['sub_mch_id'])) {
            $this->app->exception->invalidArgument('子服务商商户号不能为空: sub_mch_id');
        }
        parent::__construct($config);
    }
    
    /**
     * 用户入场通知
     * @param string $plateNumber 车牌号
     * @param string $parkingName 所在停车场的名称
     * @param int    $freeTime    免费的时间长 单位为秒
     * @param array  $extra
     * @return array
     */
    public function notification($plateNumber, $parkingName, $freeTime = 0, $extra = [])
    {
        $scene_info['scene_info'] = array_merge([
            'start_time'   => date('YmdHis'),
            'plate_number' => $plateNumber,
            'parking_name' => $parkingName,
            'free_time'    => $freeTime,
        ], $extra);
        $data['trade_scene']      = 'PARKING';
        $data['scene_info']       = json_encode($scene_info, JSON_UNESCAPED_UNICODE);
        return $this->api(self::API_NOTIFICATION, $data);
    }
    
    /**
     * 申请扣款
     * @param $params
     * @return array
     */
    public function payApply($params)
    {
        if (empty($params['out_trade_no'])) {
            return $this->app->exception->invalidArgument('商户订单号不能为空: out_trade_no');
        }
        if (empty($params['total_fee'])) {
            return $this->app->exception->invalidArgument('商户订单号不能为空(单位为分): total_fee');
        }
        if (empty($params['start_time'])) {
            return $this->app->exception->invalidArgument('用户进入停车时间不能为空(格式为yyyyMMddHHmmss): start_time');
        }
        if (empty($params['end_time'])) {
            $params['end_time'] = '';
        }
        if (empty($params['charging_time'])) {
            return $this->app->exception->invalidArgument('计费的时间长不能为空(单位为秒): charging_time');
        }
        if (empty($params['plate_number'])) {
            return $this->app->exception->invalidArgument('车牌号不能为空: plate_number');
        }
        if (empty($params['car_type'])) {
            $params['car_type'] = '小型车';
        }
        if (empty($params['parking_name'])) {
            return $this->app->exception->invalidArgument('所在停车场的名称不能为空: parking_name');
        }
        if (empty($params['create_ip'])) {
            if (empty($_SERVER['SERVER_ADDR'])) {
                return $this->app->exception->invalidArgument('请求客户IP不能为空: create_ip');
            } else {
                $params['create_ip'] = $_SERVER['SERVER_ADDR'];
            }
        }
        if (empty($params['notify_url'])) {
            return $this->app->exception->invalidArgument('微信支付异步通知地址不能为空: notify_url');
        }
        $data = array(
            'body'             => '停车场无感支付测试',//商品描述
            'detail'           => '停车场无感支付测试',//商品详情
            'out_trade_no'     => $params['out_trade_no'],
            'total_fee'        => $params['total_fee'],
            'fee_type'         => 'CNY',
            'spbill_create_ip' => $params['create_ip'],
            'notify_url'       => $params['notify_url'],
            'trade_type'       => 'PAP',
            'trade_scene'      => 'PARKING',
            'scene_info'       => json_encode(['scene_info' => [
                'start_time'    => $params['start_time'],
                'end_time'      => $params['end_time'],
                'charging_time' => $params['charging_time'],
                'plate_number'  => $params['plate_number'],
                'car_type'      => $params['car_type'],
                'parking_name'  => $params['parking_name'],
            ]], JSON_UNESCAPED_UNICODE),
        );
        return $this->api(self::API_PAY_APPLY, $data);
    }
    
    /**
     * 用户状态查询
     * @param $openId
     * @param $plateNumber
     * @return bool|mixed
     */
    public function queryState($openId, $plateNumber)
    {
        $data   = [
            'trade_scene'  => 'PARKING',
            'openid'       => $openId,
            'plate_number' => $plateNumber
        ];
        $result = $this->api(self::API_QUERY_STATE, $data);
        return $result;
    }
    
    /**
     * 查询订单
     * @param $outTradeNo
     * @return array
     */
    public function queryOrder($outTradeNo)
    {
        $data = [
            'out_trade_no' => $outTradeNo,
        ];
        return $this->api(self::API_QUERY_ORDER, $data);
    }
    
    /**
     * 下载对账单
     * @param string $billDate 下载对账单的日期，格式：20140603
     * @param string $billType ALL，返回当日所有订单信息，默认值 SUCCESS，返回当日成功支付的订单 REFUND，返回当日退款订单 RECHARGE_REFUND，返回当日充值退款订单
     * @return bool|mixed
     */
    public function downloadBill($billDate, $billType = 'ALL')
    {
        $data = [
            'bill_date' => $billDate,
            'bill_type' => $billType,
        ];
        return $this->api(self::API_DOWNLOAD_BILL, $data);
    }
    
    /**
     * curl POST
     * @param     $url
     * @param     $data
     * @return array|string
     */
    private function api($url, $data)
    {
        $params = array(
            'appid'      => $this->appid,
            'sub_appid'  => $this->sub_appid,
            'mch_id'     => $this->mch_id,
            'sub_mch_id' => $this->sub_mch_id,
            'nonce_str'  => Helper::generateRandStr(),
            'sign_type'  => 'HMAC-SHA256',//注 先使用md5 不行尝试 默认的HMAC-SHA256
        );
        // 公共参数和接口参数合并
        $data = Helper::arrayMerge($params, $data);
        // 生成签名
        $data['sign'] = Helper::makeSignSha256($data, $this->key);
        // 发送请求数据
        $result = $this->app->request->postXml($url, $data);
        // 业务受理失败
        if ($result->returnCode != 'SUCCESS') {
            return $this->app->exception->request($result->returnCode);
        }
        // 处理结果失败
        if ($result->resultCode != 'SUCCESS') {
            return $this->app->exception->request($result->resultCode);
        }
        return $result->data();
    }
    
}
