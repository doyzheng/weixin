<?php

namespace doyzheng\weixin\parking;

use doyzheng\weixin\base\BaseWeixin;
use doyzheng\weixin\base\TraitNotify;
use doyzheng\weixin\core\Helper;

/**
 * 微信车主平台异步通知处理类
 * Class Notify
 * @package doyzheng\weixin\parking
 */
class Notify extends BaseWeixin
{
    
    use TraitNotify;
    
    /**
     * @var string
     */
    public $key;
    
    /**
     * 用户无感支付状态变更通知
     * @param $callback
     * @return bool
     */
    public function userStartChange($callback)
    {
        return $this->common($callback);
    }
    
    /**
     * @param $callback
     * @return bool
     */
    public function payResult($callback)
    {
        return $this->common($callback);
    }
    
    /**
     * @param $callback
     * @return bool
     */
    private function common($callback)
    {
        $notifyData = $this->request->getRawDataXml();
        
        if (empty($notifyData)) {
            return $this->exceptionLogic('通知数据为空或格式错误');
        }
        
        if (empty($notifyData['sign'])) {
            return $this->exceptionLogic('签名参数不能为空: sign');
        }
        
        // 验证签名
        $sign     = $notifyData['sign'];
        $signData = $notifyData;
        unset($signData['sign']);
        
        if (Helper::makeSignSha256($signData, $this->key) != $sign) {
            return $this->exceptionLogic('签名验证失败');
        }
        
        // 回调
        $msg = '';
        if ($callback) {
            $msg = call_user_func_array($callback, [$notifyData]);
            if ($msg === true) {
                return $this->success();
            }
        }
        return $this->fail($msg);
    }
    
}
