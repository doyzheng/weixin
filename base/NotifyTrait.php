<?php

namespace doyzheng\weixin\base;

/**
 * 微信异步通知复用类
 * Trait BaseNotify
 * @package doyzheng\weixin\base
 */
trait NotifyTrait
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
     * 回复微信数据
     * @param array $data
     * @return bool
     */
    public function replyWeixin($data)
    {
        return Helper::array2xml($data);
    }
    
    /**
     * 获取请求原数据
     * @return mixed
     */
    public static function getRawData()
    {
        $data = file_get_contents('php://input');
        return $data;
    }
    
    /**
     *  获取请求原数据xml
     * @return array
     */
    public static function getRawDataXml()
    {
        if ($data = static::getRawData()) {
            return Helper::xml2array($data);
        }
        return [];
    }
    
    /**
     * 获取请求原数据json
     * @return array
     */
    public static function getRawDataJson()
    {
        if ($data = static::getRawData()) {
            return Helper::jsonDecode($data);
        }
        return [];
    }
}

