<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\base\interfaces\ResultInterface;

/**
 * 接口返回接口解析类
 * Class Result
 * @package doyzheng\weixin\base
 * @property int    $errCode
 * @property string $errMsg
 * @property string $returnCode
 * @property string $returnMsg
 * @property string $resultCode
 */
class Result implements ResultInterface
{
    
    /**
     * 接口返回原内容
     * @var string
     */
    public $content;
    
    /**
     * curl请求信息
     * @var CurlInfo
     */
    public $info;
    
    /**
     * 解析后的数据
     * @var mixed
     */
    private $_data;
    
    /**
     * 是否解析过
     * @var bool
     */
    private $_isParse;
    
    /**
     * @var array 属性对照表
     */
    private $_fieldWords = [
        'errCode'    => 'errcode',
        'errMsg'     => 'errmsg',
        'returnCode' => 'return_code',
        'returnMsg'  => 'return_msg',
        'resultCode' => 'result_code',
    ];
    
    /**
     * Result constructor.
     * @param array  $curlInfo
     * @param string $content
     */
    public function __construct($curlInfo, $content)
    {
        $this->info    = new CurlInfo($curlInfo);
        $this->content = $content;
    }
    
    /**
     * 自动解析接口返回的内容
     * @return array|null
     */
    public function parseContent()
    {
        if ($this->info->contentType == 'json') {
            return $this->parseJson();
        }
        if ($this->info->contentType == 'xml') {
            return $this->parseXml();
        }
        // 最后使用json方式解析
        if ($data = $this->parseJson()) {
            return $data;
        }
        return null;
    }
    
    /**
     * 解析json格式字符串
     * @return array
     */
    public function parseJson()
    {
        return Helper::jsonDecode($this->content);
    }
    
    /**
     * 解析Xml格式字符串
     * @return array
     */
    public function parseXml()
    {
        return Helper::jsonDecode($this->content);
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data($name);
    }
    
    /**
     * 获取解析后的数据
     * @param null $name
     * @return array|string|null
     */
    public function data($name = null)
    {
        if (!$this->_isParse) {
            $this->_data    = $this->parseContent();
            $this->_isParse = true;
        }
        if ($name === null) {
            return $this->_data;
        }
        if (!is_array($this->_data) || !$name) {
            return null;
        }
        if (isset($this->_fieldWords[$name])) {
            $name = $this->_fieldWords[$name];
        }
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return null;
    }
    
    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->data($offset) !== null;
    }
    
    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data($offset);
    }
    
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }
    
    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if (isset($this->_data[$offset])) {
            unset($this->_data[$offset]);
        }
    }
    
}