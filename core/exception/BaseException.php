<?php

namespace doyzheng\weixin\core\exception;

/**
 * Class BaseException
 * @package doyzheng\weixin\core\exception
 */
abstract class BaseException extends \LogicException
{
    
    /**
     * BaseException constructor.
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}
