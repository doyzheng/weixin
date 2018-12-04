<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\base\interfaces\RequestInterface;

/**
 * 接口请求类
 * Class Request
 * @package doyzheng\weixin\base
 */
class Request extends BaseObject implements RequestInterface
{
    
    /**
     * @var array 请求历史记录
     */
    public $history = [];
    
    /**
     *
     */
    public function init()
    {
        if (!extension_loaded('curl')) {
            $this->app->exception->error('The curl extension is not open');
        }
    }
    
    /**
     * post方式请求
     * @param string $url
     * @param mixed  $data
     * @param array  $options
     * @return Result|mixed
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
        return $this->curl($url, $options);
    }
    
    /**
     * post方式发送json格式数据
     * @param string $url
     * @param array  $data
     * @param array  $options
     * @return Result|mixed
     */
    public function postJson($url, $data, $options = [])
    {
        return $this->post($url, Helper::jsonEncode($data), $options);
    }
    
    /**
     * post方式发送Xml格式数据
     * @param string $url
     * @param array  $data
     * @param array  $options
     * @return Result|mixed
     */
    public function postXml($url, $data, $options = [])
    {
        return $this->post($url, Helper::array2xml($data), $options);
    }
    
    /**
     * get请求
     * @param string $url
     * @param array  $params
     * @param array  $options
     * @return Result|mixed
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
        $content = curl_exec($ch);
        $error   = curl_error($ch);
        $result  = new Result($content, curl_getinfo($ch));
        $this->after($url, $options, $result, $ch);
        curl_close($ch);
        if ($error) {
            return $this->app->exception->request('请求接口异常: ' . $error);
        }
        return $result;
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
     */
    public function after($url, $options, $result, $ch)
    {
        $data = [
            'url'     => $url,
            'options' => $this->curlOption2name($options),
            'result'  => [
                'content'  => $result->getContent(),
                'curlInfo' => json_encode($result->getCurlInfo()->getData(), true),
                'data'     => $result->getData(),
            ]
        ];
        // 记录请求历史
        $this->history[] = $data;
        // 写请求日志
        $this->app->log->request($data);
    }
    
    /**
     * curl参数转换为常量名称
     * @param array $options
     * @return array
     */
    private function curlOption2name($options)
    {
        $def      = get_defined_constants(true);
        $curlList = array_flip($def['curl']);
        $list     = [];
        foreach ($options as $name => $val) {
            $list[$curlList[$name]] = $val;
        }
        return $list;
    }
    
}