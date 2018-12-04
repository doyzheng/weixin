<?php

namespace doyzheng\weixin\base;

/**
 * Class CurlInfo
 * @package doyzheng\weixin\base
 * @property string url
 * @property string contentType
 * @property string httpCode
 * @property string headerSize
 * @property string requestSize
 * @property string filetime
 * @property string sslVerifyResult
 * @property string redirectCount
 * @property string totalTime
 * @property string namelookupTime
 * @property string connectTime
 * @property string pretransferTime
 * @property string sizeUpload
 * @property string sizeDownload
 * @property string speedDownload
 * @property string speedUpload
 * @property string downloadContentLength
 * @property string uploadContentLength
 * @property string starttransferTime
 * @property string redirectTime
 * @property string redirectUrl
 * @property string primaryIp
 * @property string certinfo
 * @property string primaryPort
 * @property string localIp
 * @property string localPort
 */
class CurlInfo extends BaseArrayAccess
{
    
    /**
     * CurlInfo constructor.
     * @param $info
     */
    public function __construct($curlInfo = [])
    {
        foreach ($curlInfo as $key => $value) {
            $this->offsetSet(Helper::parseName($key, true, false), $value);
        }
    }
    
    /**
     * 获取返回数据类型
     */
    protected function getContentType()
    {
        $contentType = $this->getData('contentType');
        if (strstr($contentType, '/json') !== false) {
            return 'json';
        }
        if (strstr($contentType, 'xml') !== false) {
            return 'xml';
        }
        return $contentType;
    }
    
}