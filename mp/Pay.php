<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\BaseWeixin;
use doyzheng\weixin\core\Helper;

/**
 * 公众号支付
 * Class Pay
 * @package doyzheng\weixin\mini\payment
 * @link    https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_1
 */
class Pay extends BaseWeixin
{
    
    /**
     * @var string 公众账号ID
     */
    public $appid;
    
    /**
     * @var string 商户号
     */
    public $mch_id;
    
    /**
     * @var string  key设置路径：微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置
     */
    public $key;
    
    /**
     * Pay constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (empty($config['appid'])) {
            $this->exception->invalidArgument('公众账号ID不能为空: appid');
        }
        
        if (empty($config['mch_id'])) {
            $this->exception->invalidArgument('商户号不能为空: mch_id');
        }
        
        if (empty($config['key'])) {
            $this->exception->invalidArgument('商户Api秘钥为空: key');
        }
        parent::__construct($config);
    }
    
    /**
     * @var array Api列表
     */
    private $apiUrls = [
        // 统一下单
        'unifiedOrder'      => 'https://api.mch.weixin.qq.com/pay/unifiedorder',
        // 查询订单
        'orderQuery'        => 'https://api.mch.weixin.qq.com/pay/orderquery',
        // 关闭订单
        'closeOrder'        => 'https://api.mch.weixin.qq.com/pay/closeorder',
        // 申请退款
        'refund'            => 'https://api.mch.weixin.qq.com/secapi/pay/refund',
        // 查询退款
        'refundQuery'       => 'https://api.mch.weixin.qq.com/pay/refundquery',
        // 下载对账单
        'downloadBill'      => 'https://api.mch.weixin.qq.com/pay/downloadbill',
        // 下载资金账单
        'downloadFundFlow'  => 'https://api.mch.weixin.qq.com/pay/downloadfundflow',
        // 拉取订单评价数据
        'batchQueryComment' => 'https://api.mch.weixin.qq.com/billcommentsp/batchquerycomment',
    ];
    
    /**
     * 统一下单
     * @param array $params
     * @return array|mixed|null
     */
    public function unifiedOrder(array $params)
    {
        if (empty($params['body'])) {
            return $this->exception->invalidArgument('商品描述不能为空: body');
        }
        if (empty($params['out_trade_no'])) {
            return $this->exception->invalidArgument('商户订单号不能为空: out_trade_no');
        }
        if (empty($params['total_fee'])) {
            return $this->exception->invalidArgument('标价金额不能为空: total_fee');
        }
        if (empty($params['notify_url'])) {
            return $this->exception->invalidArgument('通知地址不能为空: notify_url');
        }
        if (empty($params['openid'])) {
            return $this->exception->invalidArgument('用户标识不能为空: openid');
        }
        
        // 请求参数
        $requestParams = [
            'body'             => '',// 商品描述
            'out_trade_no'     => '',// 商户订单号
            'total_fee'        => '1',// 标价金额
            'notify_url'       => '',// 通知地址 异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
            'openid'           => '',// 用户标识 trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。openid如何获取，可参考【获取openid】。
            'device_info'      => null,// 设备号
            'detail'           => null,// 商品详情
            'attach'           => null,//  附加数据
            'fee_type'         => 'CNY',// 标价币种
            'spbill_create_ip' => Helper::getClientIp(),// 终端IP APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
            'time_start'       => null,// 交易起始时间
            'time_expire'      => null,// 交易结束时间
            'goods_tag'        => null,// 订单优惠标记
            'trade_type'       => 'JSAPI',// 交易类型
            'product_id'       => null,// 商品Id trade_type=NATIVE时（即扫码支付），此参数必传。此参数为二维码中包含的商品ID，商户自行定义。
            'limit_pay'        => null,// 指定支付方式
        ];
        return $this->api($this->apiUrls['unifiedOrder'], Helper::arrayMerge($requestParams, $params));
    }
    
    /**
     * 查询订单
     * @param string $outTradeNo
     * @return array|mixed|null
     */
    public function orderQuery($outTradeNo)
    {
        // 如果订单号是28位纯数字认为是微信内部订单号
        if (strlen($outTradeNo) == 28 && is_numeric($outTradeNo)) {
            $requestParams = [
                'transaction_id' => $outTradeNo,
            ];
        } else {
            $requestParams = [
                'out_trade_no' => $outTradeNo,
            ];
        }
        return $this->api($this->apiUrls['orderQuery'], $requestParams);
    }
    
    /**
     * 关闭订单
     * @param string $outTradeNo
     * @return array|mixed|null
     */
    public function closeOrder($outTradeNo)
    {
        // 请求参数
        $requestParams = [
            'out_trade_no' => $outTradeNo,
        ];
        return $this->api($this->apiUrls['closeOrder'], $requestParams);
    }
    
    /**
     * 申请退款
     * @param array $params
     * @param array $options
     * @return array|mixed
     */
    public function refund(array $params, $options = [])
    {
        if (empty($params['transaction_id']) && empty($params['out_trade_no'])) {
            return $this->exception->invalidArgument('微信订单号,商户订单号必须二选一: transaction_id,out_trade_no');
        }
        if (empty($params['out_refund_no'])) {
            return $this->exception->invalidArgument('商户退款单号不能为空: out_refund_no');
        }
        if (empty($params['total_fee'])) {
            return $this->exception->invalidArgument('订单金额不能为空: total_fee');
        }
        if (empty($params['refund_fee'])) {
            return $this->exception->invalidArgument('退款金额不能为空: refund_fee');
        }
        if (empty($params['notify_url'])) {
            return $this->exception->invalidArgument('退款结果通知地址不能为空: notify_url');
        }
        if (empty($options['cert.pem'])) {
            return $this->exception->invalidArgument('证书文件名不能为空: cert.pem');
        }
        if (empty($options['key.pem'])) {
            return $this->exception->invalidArgument('证书文件名不能为空: key.pem');
        }
        // 请求参数
        $requestParams = [
            'transaction_id'  => '',// 微信订单号
            'out_trade_no'    => '',// 商户订单号
            'out_refund_no'   => '',// 商户退款单号
            'total_fee'       => '',// 订单金额
            'refund_fee'      => '',// 退款金额
            'refund_fee_type' => 'CNY',// 货币种类
            'refund_desc'     => null,// 退款原因
            'refund_account'  => null,// 退款资金来源
            'notify_url'      => '',// 退款结果通知url
        ];
        $option        = [
            CURLOPT_SSLCERTTYPE => 'PEM',
            CURLOPT_SSLKEYTYPE  => 'PEM',
            CURLOPT_SSLCERT     => $options['cert.pem'],
            CURLOPT_SSLKEY      => $options['key.pem'],
        ];
        return $this->api($this->apiUrls['refund'], Helper::arrayMerge($requestParams, $params), $option);
    }
    
    /**
     * 查询退款
     * @param array $params
     * @return array|mixed|null
     */
    public function refundQuery(array $params)
    {
        if (empty($params['transaction_id']) && empty($params['out_trade_no']) && empty($params['out_refund_no']) && empty($params['refund_id'])) {
            return $this->exception->invalidArgument('微信订单号,商户订单号,商户退款单号,微信退款单号必须四选一: transaction_id, out_trade_no, out_refund_no, refund_id');
        }
        // 请求参数
        $requestParams = [
            'transaction_id' => '',// 微信订单号
            'out_trade_no'   => '',// 商户订单号
            'out_refund_no'  => '',// 商户退款单号
            'refund_id'      => '',// 微信退款单号
            'offset'         => '',// 偏移量 偏移量，当部分退款次数超过10次时可使用，表示返回的查询结果从这个偏移量开始取记录
        ];
        return $this->api($this->apiUrls['refundQuery'], Helper::arrayMerge($requestParams, $params));
    }
    
    /**
     * 下载对账单
     * @param string $billDate  对账单日期 下载对账单的日期，格式：20140603
     * @param string $billType  ALL，返回当日所有订单信息，默认值
     *                          SUCCESS，返回当日成功支付的订单
     *                          REFUND，返回当日退款订单
     *                          RECHARGE_REFUND，返回当日充值退款订单（相比其他对账单多一栏“返还手续费”）
     * @return array|mixed|null
     */
    public function downloadBill($billDate, $billType = 'ALL')
    {
        // 请求参数
        $requestParams = [
            'bill_date' => $billDate,
            'bill_type' => $billType,
            'tar_type'  => 'GZIP',// 压缩账单
        ];
        return $this->api($this->apiUrls['downloadBill'], $requestParams);
    }
    
    /**
     * 下载资金账单
     * @param string $billDate    资金账单日期
     * @param string $accountType 账单的资金来源账户： Basic  基本账户 Operation 运营账户  Fees 手续费账户
     * @return string
     */
    public function downloadFundFlow($billDate, $accountType)
    {
        // 请求参数
        $requestParams = [
            'bill_date'    => $billDate,
            'account_type' => $accountType,
            'tar_type'     => 'GZIP',// 压缩账单
        ];
        return $this->api($this->apiUrls['downloadFundFlow'], $requestParams);
    }
    
    /**
     * 拉取订单评价数据
     * @param string $begin_time 开始时间 按用户评论时间批量拉取的起始时间，格式为yyyyMMddHHmmss
     * @param string $end_time   结束时间 按用户评论时间批量拉取的结束时间，格式为yyyyMMddHHmmss
     * @param string $offset     位移 指定从某条记录的下一条开始返回记录。接口调用成功时，会返回本次查询最后一条数据的offset。商户需要翻页时，应该把本次调用返回的offset 作为下次调用的入参。注意offset是评论数据在微信支付后台保存的索引，未必是连续的
     * @param string $limit      条数 一次拉取的条数, 最大值是200，默认是200
     * @return array|mixed|null
     */
    public function batchQueryComment($begin_time, $end_time, $offset, $limit = '100')
    {
        // 请求参数
        $requestParams = [
            'begin_time' => $begin_time,
            'end_time'   => $end_time,
            'offset'     => $offset,
            'limit'      => $limit,
        ];
        return $this->api($this->apiUrls['batchQueryComment'], $requestParams);
    }
    
    /**
     * @param       $url
     * @param       $params
     * @param array $options
     * @return array|mixed
     */
    private function api($url, $params, $options = [])
    {
        // 公共参数
        $commonParams   = [
            'appid'     => $this->appid,// 公众账号ID
            'mch_id'    => $this->mch_id,// 商户号
            'nonce_str' => Helper::generateRandStr(),
            'sign_type' => 'MD5',
            'sign'      => '',
        ];
        $params         = Helper::arrayMerge(array_filter($commonParams), $params);
        $params['sign'] = Helper::makeSignMd5($params, $this->key);
        
        $result = $this->request->postXml($url, $params, $options);
        $data   = $result->parseXml();
        
        if (isset($data['return_msg']) && $data['return_code'] != 'SUCCESS') {
            return $this->exception->error($data['return_msg']);
        }
        
        return $data ? $data : $result->content;
    }
    
}
