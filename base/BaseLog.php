<?php

namespace doyzheng\weixin\base;

/**
 * 日志处理基类
 * Class BaseLog
 * @package doyzheng\weixin\base
 */
abstract class BaseLog extends BaseWeixin
{
    
    /**
     * @var string 日志保存目录
     */
    public $savePath;
    
    /**
     * @var bool 是否禁用日志
     */
    public $disable = true;
    
    /**
     * 添加日志数
     * @param string $type
     * @param mixed  $data
     * @return bool
     */
    abstract public function add($type, $data);
    
    /**
     * 请求异常日志
     * @param array $data
     * @return mixed
     */
    abstract public function weixinError($data);
    
    /**
     * 请求日志
     * @param array $data
     * @return mixed
     */
    abstract public function request($data);
    
    /**
     * 访问日志
     * @param string $name
     * @return mixed
     */
    abstract public function access($name = '');
    
    /**
     * 异常日志
     * @param mixed $exception
     * @return mixed
     */
    abstract public function error($exception);
    
}
