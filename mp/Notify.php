<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\Helper;
use doyzheng\weixin\base\NotifyTrait;

/**
 * 公众支付通知
 * Class Notify
 * @package doyzheng\weixin\mini
 */
class Notify extends Base
{
    
    use NotifyTrait;
    
    /**
     * @var string  商户Api秘钥
     */
    public $key;
    
    /**
     * Notify constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (empty($config['key'])) {
            $this->app->exception->invalidArgument('商户Api秘钥为空: key');
        }
        parent::__construct($config);
    }
    
    /**
     * 支付结果通知回调
     * @param $callback
     * @return bool
     */
    public function payResult($callback)
    {
        $data = self::getRawDataXml();
        if (empty($data)) {
            return $this->app->exception->notify('微信通知数据异常');
        }
        if ($data['return_code'] != 'SUCCESS') {
            return $this->app->exception->notify('微信通知结果异常');
        }
        
        $sign   = $data['sign'];
        $params = $data;
        unset($params['sign']);
        if (Helper::makeSignMd5($params, $this->key) == $sign) {
            return $this->app->exception->notify('签名验证错误');
        }
        
        $res = call_user_func_array($callback, [$data]);
        if ($res === true) {
            return $this->success();
        }
        return $this->fail($res);
    }
    
    /**
     * 退款结果通知回调
     * @param $callback
     * @return bool|string
     */
    public function refundResult($callback)
    {
        $data = self::getRawDataXml();
        
        if (empty($data['req_info'])) {
            return $this->app->exception->notify('微信通知数据异常');
        }
        // 解密微信通知的数据
        $base64 = base64_decode($data['req_info']);
        $xml = openssl_decrypt($base64, 'aes-256-ecb', md5($this->key), OPENSSL_RAW_DATA);
        if (empty($xml)) {
            return $this->app->exception->notify('微信通知数据解密失败');
        }
        $res = call_user_func_array($callback, [Helper::xml2array($xml)]);
        if ($res === true) {
            return $this->success();
        }
        return $this->fail($res);
    }
    
}
