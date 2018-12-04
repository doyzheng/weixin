<?php

namespace doyzheng\weixin\base;

/**
 * 接口返回接口解析类
 * Class Result
 * @package doyzheng\weixin\base
 * @property int    $errcode
 * @property string $errmsg
 * @property string $return_code
 * @property string $return_msg
 * @property string $result_code
 */
class Result extends BaseArrayAccess
{
    
    /**
     * 接口返回原内容
     * @var string
     */
    private $_content;
    
    /**
     * curl请求信息
     * @var CurlInfo
     */
    private $_curlInfo;
    
    /**
     * Result constructor.
     * @param string $content
     * @param array  $curlInfo
     */
    public function __construct($content, $curlInfo)
    {
        $this->_content  = $content;
        $this->_curlInfo = new CurlInfo($curlInfo);
        $this->setData($this->parseContent($content));
    }
    
    /**
     * 返回请求结果原内容
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }
    
    /**
     * 当结果当做字符串时直接返回原内容
     * @return string
     */
    public function __toString()
    {
        return $this->_content;
    }
    
    /**
     * 返回curl信息
     * @return CurlInfo
     */
    public function getCurlInfo()
    {
        return $this->_curlInfo;
    }
    
    /**
     * 自动解析接口返回的内容
     * @param string $content
     * @return array
     */
    protected function parseContent($content)
    {
        if ($this->getCurlInfo()->contentType == 'json') {
            return Helper::jsonDecode($content);
        }
        if ($this->getCurlInfo()->contentType == 'xml') {
            return Helper::xml2array($content);
        }
        // 最后使用json方式解析
        if ($data = Helper::jsonDecode($content)) {
            return $data;
        }
        return [];
    }
    
}