<?php

namespace doyzheng\weixin\base\interfaces;

/**
 * Interface AccessToken
 * @package doyzheng\weixin\base
 */
interface RequestInterface
{
    
    /**
     * post方式请求
     * @param string       $url
     * @param array|string $data
     * @param array        $options
     * @return mixed
     */
    public function post($url, $data, $options = []);
    
    /**
     * get方法请求
     * @param       $url
     * @param array $params
     * @param array $options
     * @return ResultInterface
     */
    public function get($url, $params = [], $options = []);
    
}