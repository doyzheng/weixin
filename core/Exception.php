<?php

namespace doyzheng\weixin\core;

use doyzheng\weixin\base\BaseWeixin;
use doyzheng\weixin\core\exception\WxErrorException;
use doyzheng\weixin\core\exception\WxInvalidArgumentException;
use doyzheng\weixin\core\exception\WxLogicException;
use doyzheng\weixin\core\exception\WxNotifyException;
use doyzheng\weixin\core\exception\WxUnknownClassException;

/**
 * Class Exception
 * @package doyzheng\weixin\core
 */
class Exception extends BaseWeixin
{
    
    /**
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function unknownClass($message = "", $code = 0)
    {
        $exception = new WxUnknownClassException($message, $code);
        $this->log->error($exception);
        if ($this->container->weixin->appDebug) {
            throw $exception;
        }
        exit;
    }
    
    /**
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function invalidArgument($message = "", $code = 0)
    {
        $exception = new WxInvalidArgumentException($message, $code);
        $this->log->error($exception);
        if ($this->container->weixin->appDebug) {
            throw $exception;
        }
        exit;
    }
    
    /**
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function error($message = "", $code = 0)
    {
        $exception = new WxErrorException($message, $code);
        $this->log->weixinError($this->request->getLastRequestHistory());
        if ($this->container->weixin->appDebug) {
            throw $exception;
        }
        exit;
    }
    
    /**
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function logic($message = "", $code = 0)
    {
        $exception = new WxLogicException($message, $code);
        $this->log->error($exception);
        if ($this->container->weixin->appDebug) {
            throw $exception;
        }
        exit;
    }
    
    /**
     * @param string $message
     * @param int    $code
     * @return mixed
     */
    public function notify($message = "", $code = 0)
    {
        $exception = new WxNotifyException($message, $code);
        $this->log->add('notify', $message);
        if ($this->container->weixin->appDebug) {
            throw $exception;
        }
        exit;
    }
    
}