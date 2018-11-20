<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\core\Helper;
use doyzheng\weixin\core\request\Result;

/**
 * Class BaseRequest
 * @package doyzheng\weixin\base
 */
abstract class BaseRequest extends BaseWeixin
{
    
    /**
     * @var string 设置代理
     */
    public $userAgent = 'Doyzheng-Curl-Agent';
    
    /**
     * @var int 超时设置
     */
    public $timeout = 30;
    
    /**
     * @var Result[] 请求历史记录
     */
    public $historys;
    
    /**
     * GET方式发送数据
     * @param string $url
     * @param array  $query
     * @param array  $options
     * @return Result|null
     */
    abstract public function get($url, $query = [], $options = []);
    
    /**
     * @param string $url
     * @param array  $query
     * @param array  $options
     * @return array|mixed
     */
    abstract public function getJson($url, $query = [], $options = []);
    
    /**
     * POST方式发送数据
     * @param string $url
     * @param array  $data
     * @param array  $options
     * @return Result|null
     */
    abstract public function post($url, $data, $options = []);
    
    /**
     * POST方式发送json格式数据
     * @param string $url
     * @param array  $data
     * @param array  $options
     * @return Result|null
     */
    abstract public function postJson($url, $data, $options = []);
    
    /**
     * POST方式发送Xml格式数据
     * @param string $url
     * @param array  $data
     * @param array  $options
     * @return mixed
     */
    abstract public function postXml($url, $data, $options = []);
    
    /**
     * 发送请求前方调用
     * @param string $url
     * @param array  $options
     * @return Result|null|void
     */
    abstract public function before($url, $options);
    
    /**
     * 请求结束后调用
     * @param string $url
     * @param array  $options
     * @param string $content
     * @param mixed  $ch
     * @return Result|null|void
     */
    abstract public function after($url, $options, $content, $ch);
    
    /**
     * @param $url
     * @return mixed
     */
    abstract public function parseUrl($url);
    
    /**
     * 获取最后一次请求记录
     * @return mixed|null
     */
    abstract public function getLastRequestHistory();
    
    /**
     * 获取当前的请求的url地址
     * @return string
     */
    public static function getSelfUrl()
    {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self     = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info    = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url   = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }
    
    /**
     * @return mixed
     */
    public static function getParams()
    {
        return $_REQUEST;
    }
    
    public static function getParam($name, $default = null)
    {
    
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
