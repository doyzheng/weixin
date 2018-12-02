<?php

namespace doyzheng\weixin\base\interfaces;

/**
 * Interface AccessToken
 * @package doyzheng\weixin\base
 */
interface ExceptionInterface
{
    
    /**
     * 当调用一个不存在的对象时抛出异常
     * @param string $message
     * @param int    $code
     */
    public function unknownClass($message = "", $code = 0);
    
    /**
     * 当调用一个不存在的对象方法时抛出异常
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function unknownMethod($message = "", $code = 0);
    
    /**
     * 当调用一个不存在的对象属性时抛出异常
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function unknownProperty($message = "", $code = 0);
    
    /**
     * 传入一个无效参数时抛出异常
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function invalidArgument($message = "", $code = 0);
    
    /**
     * 当业务逻辑错误时抛出异常
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function error($message = "", $code = 0);
    
    /**
     * 请求接口返回失败时抛出异常
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function request($message = "", $code = 0);
    
    /**
     * 当处理微信异步通知业务时抛出
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function notify($message = "", $code = 0);
    
}