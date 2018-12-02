<?php

namespace doyzheng\weixin\base\interfaces;

/**
 * 微信结果返回类
 * Interface ResultInterface
 * @package doyzheng\weixin\base\interfaces
 */
interface LogInterface
{
    
    /**
     * 获取解析后的数据
     * @param string $name
     * @return array|string
     */
    public function add($type, $data);
    
    /**
     * 记录请求日志
     * @param array $data
     * @return mixed
     */
    public function request($data);
    
    /**
     * 记录错误日志
     * @param \Exception $exception
     * @return mixed
     */
    public function error($exception);
    
    /**
     * 记录访问日志
     * @param string $name
     * @return mixed
     */
    public function access($name = '');
    
}
