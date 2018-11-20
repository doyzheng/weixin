<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\core\Helper;

/**
 * 微信异步通知复用类
 * Trait BaseNotify
 * @package doyzheng\weixin\base
 */
trait TraitNotify
{
    
    /**
     * 成功返回
     * @return bool
     */
    public function success()
    {
        $data = [
            'return_code' => 'SUCCESS',
            'return_msg'  => 'OK',
        ];
        return $this->replyWeixin($data);
    }
    
    /**
     * 失败返回
     * @param $msg
     * @return bool
     */
    public function fail($msg)
    {
        $data = [
            'return_code' => 'FAIL',
            'return_msg'  => $msg,
        ];
        return $this->replyWeixin($data);
    }
    
    /**
     * @param array $data
     * @return bool
     */
    public function replyWeixin($data)
    {
        return Helper::array2xml($data);
    }
    
}
