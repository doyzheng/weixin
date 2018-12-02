<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\base\exception\WxErrorException;
use doyzheng\weixin\base\exception\WxInvalidArgumentException;
use doyzheng\weixin\base\exception\WxNotifyException;
use doyzheng\weixin\base\exception\WxRequestException;
use doyzheng\weixin\base\exception\WxUnknownClassException;
use doyzheng\weixin\base\exception\WxUnknownMethodException;
use doyzheng\weixin\base\exception\WxUnknownPropertyException;
use doyzheng\weixin\base\interfaces\ExceptionInterface;

/**
 * Class Exception
 * @package doyzheng\weixin\core
 */
class Exception extends BaseObject implements ExceptionInterface
{
    
    /**
     * 抛异常
     * @param $exception
     */
    private function throwException($exception)
    {
        $this->app->log->error($exception);
        if ($this->app->appDebug) {
            throw $exception;
        }
    }
    
    /**
     * 不存在的类
     * @param string $message
     * @param int    $code
     */
    public function unknownClass($message = "", $code = 0)
    {
        $this->throwException(new WxUnknownClassException($message, $code));
    }
    
    /**
     * 不存在的类方法
     * @param string $message
     * @param int    $code
     */
    public function unknownMethod($message = "", $code = 0)
    {
        $this->throwException(new WxUnknownMethodException($message, $code));
    }
    
    /**
     * 不存在的类属性
     * @param string $message
     * @param int    $code
     */
    public function unknownProperty($message = "", $code = 0)
    {
        $this->throwException(new WxUnknownPropertyException($message, $code));
    }
    
    /**
     * 无效的参数
     * @param string $message
     * @param int    $code
     */
    public function invalidArgument($message = "", $code = 0)
    {
        $this->throwException(new WxInvalidArgumentException($message, $code));
    }
    
    /**
     * 业务错误
     * @param string $message
     * @param int    $code
     */
    public function error($message = "", $code = 0)
    {
        $this->throwException(new WxErrorException($message, $code));
    }
    
    /**
     * 请求接口时错误
     * @param string $message
     * @param int    $code
     */
    public function request($message = "", $code = 0)
    {
        $data = $this->app->request->history;
        $this->app->log->request($data);
        if ($this->app->appDebug) {
            throw new WxRequestException($message, $code);
        }
    }
    
    /**
     * 异步通知数据异常
     * @param string $message
     * @param int    $code
     */
    public function notify($message = "", $code = 0)
    {
        $this->throwException(new WxNotifyException($message, $code));
    }
    
}