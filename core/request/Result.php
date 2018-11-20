<?php

namespace doyzheng\weixin\core\request;

use doyzheng\weixin\core\Helper;

/**
 * 请求结果
 * Class Result
 * @package doyzheng\weixin\core\request
 * @property string $url
 * @property string $contentType
 * @property string $httpCode
 * @property string $headerSize
 * @property string $requestSize
 * @property string $filetime
 * @property string $sslVerifyResult
 * @property string $redirectCount
 * @property string $totalTime
 * @property string $namelookupTime
 * @property string $connectTime
 * @property string $pretransferTime
 * @property string $sizeUpload
 * @property string $sizeDownload
 * @property string $speedDownload
 * @property string $speedUpload
 * @property string $downloadContentLength
 * @property string $uploadContentLength
 * @property string $starttransferTime
 * @property string $redirectTime
 */
class Result
{
    
    /**
     * @var string 请求返回的原始内容
     */
    public $content;
    
    /**
     * @var string 错误信息
     */
    public $error;
    
    /**
     * @var array 请求信息
     */
    public $info;
    
    /**
     * @var bool 是否已经解析过了
     */
    private $isParse = false;
    
    /**
     * @var array 转换后的数据
     */
    private $data;
    
    /**
     * Result constructor.
     * @param $ch
     * @param $content
     */
    public function __construct($ch, $content)
    {
        $this->content = $content;
        $this->error   = curl_error($ch);
        foreach (curl_getinfo($ch) as $name => $value) {
            $name              = Helper::parseName($name, true, false);
            $this->info[$name] = $value;
        }
    }
    
    /**
     * 根据返回内容自动转换数据
     * @param string $key
     * @return array|mixed|null
     */
    public function getData($key = '')
    {
        $data = $this->parseContent();
        return $key === '' ? $data : Helper::getValue($data, $key);
    }
    
    /**
     * 解析返回内容(支持json,xml格式转换)
     * @param string $type
     * @return array
     */
    private function parseContent($type = '')
    {
        if (!$this->isParse) {
            $this->isParse = true;
            
            // 判断为json格式并转换到数组
            if ($type == 'json') {
                if ($this->data = $this->parseJson()) {
                    return $this->data;
                }
            }
            
            // 判断为xml格式并转换到数组
            if ($type == 'xml') {
                if ($this->data = $this->parseXml()) {
                    return $this->data;
                }
            }
            
            if ($this->data = $this->parseJson()) {
                return $this->data;
            }
            
            if ($this->data = $this->parseXml()) {
                return $this->data;
            }
        }
        return $this->data;
    }
    
    /**
     * 解析xml数据格式
     * @return array
     */
    public function parseXml()
    {
        return Helper::xml2array($this->content);
    }
    
    /**
     * 解析json格式
     * @return array
     */
    public function parseJson()
    {
        return Helper::jsonDecode($this->content);
    }
    
    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (isset($this->info[$name])) {
            return $this->info[$name];
        }
        
        return null;
    }
    
}
