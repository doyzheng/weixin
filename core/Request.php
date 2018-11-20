<?php

namespace doyzheng\weixin\core;

use doyzheng\weixin\base\BaseRequest;
use doyzheng\weixin\core\request\Result;

/**
 * Curl请求类
 * Class Request
 * @package doyzheng\weixin\core
 */
class Request extends BaseRequest
{
    
    /**
     * POST方式请求数据
     * @param string       $url
     * @param array|string $data
     * @param array        $options
     * @return Result|mixed|null
     */
    public function post($url, $data, $options = [])
    {
        $options = Helper::arrayMerge([
            CURLOPT_USERAGENT      => 'Doyzheng-Curl-Agent',
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $data,
        ], $options);
        return static::curl($url, $options);
    }
    
    /**
     * GET方法请求数据
     * @param string $url
     * @param array  $params
     * @param array  $options
     * @return Result|mixed|null
     */
    public function get($url, $params = [], $options = [])
    {
        $url     .= $params ? ((strpos($url, '?') === false ? '?' : '&') . http_build_query($params)) : '';
        $options = Helper::arrayMerge([
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ], $options);
        return $this->curl($url, $options);
    }
    
    /**
     * POST方式发送json格式请求数据
     * @param string $url
     * @param array  $data
     * @param array  $options curl配置
     * @return Result|mixed|null
     */
    public function postJson($url, $data = [], $options = [])
    {
        $data    = json_encode($data, JSON_UNESCAPED_UNICODE);
        $options = Helper::arrayMerge([
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data),
            ]
        ], $options);
        return $this->post($url, $data, $options);
    }
    
    /**
     * POST方式发送Xml格式请求数据
     * @param string $url
     * @param array  $data
     * @param array  $options curl配置
     * @return Result|mixed|null
     */
    public function postXml($url, $data, $options = [])
    {
        $data    = Helper::array2xml($data);
        $options = Helper::arrayMerge([
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/xml; charset=utf-8',
                'Content-Length: ' . strlen($data),
            ]
        ], $options);
        
        return static::post($url, $data, $options);
    }
    
    /**
     * get方式请求json格式数据
     * @param string $url
     * @param array  $params
     * @param array  $options curl配置
     * @return array|mixed
     */
    public function getJson($url, $params = [], $options = [])
    {
        return $this->get($url, $params, $options)->parseJson();
    }
    
    /**
     * curl请求
     * @param string $url     请求地址
     * @param array  $options curl配置
     * @return Result|mixed
     */
    private function curl($url, $options)
    {
        $this->before($url, $options);
        
        $ch = curl_init($this->parseUrl($url));
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $error  = curl_error($ch);
        $result = new Result($ch, $result);
        $this->after($url, $options, $result, $ch);
        curl_close($ch);
        
        if ($error) {
            return $this->exception->logic('请求接口异常: ' . $error);
        }
        if (empty($result)) {
            return $this->exception->logic('请求接口无响应数据: ');
        }
        
        return $result;
    }
    
    /**
     * 发送请求前方法
     * @param $url
     * @param $options
     * @return Result|null|void
     */
    public function before($url, $options)
    {
    
    }
    
    /**
     * 请求结束后
     * @param string $url
     * @param array  $options
     * @param Result $result
     * @param mixed  $ch
     * @return Result|null|void
     */
    public function after($url, $options, $result, $ch)
    {
        $data = [
            'url'     => $url,
            'options' => $options,
            'result'  => [
                'content' => $result->content,
                'data'    => $result->getData(),
                'info'    => $result->info,
            ]
        ];
        // 记录请求历史
        $this->historys[] = $data;
        // 写请求日志
        $this->log->request($data);
    }
    
    /**
     * @param $url
     * @return mixed
     */
    public function parseUrl($url)
    {
        return $url;
    }
    
    /**
     * 获取最后一次请求记录
     * @return Result|mixed|null
     */
    public function getLastRequestHistory()
    {
        return $this->historys ? array_pop($this->historys) : null;
    }
    
}
