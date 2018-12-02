<?php

namespace doyzheng\weixin\base;

/**
 * Class CurlInfo
 * @package doyzheng\weixin\base
 * @property string url
 * @property string contentType
 * @property string httpCode
 * @property string headerSize
 * @property string fileTime
 * @property string requestSize
 * @property string redirectCount
 * @property string sslVerifyResult
 * @property string totalTime
 * @property string nameLookupTime
 * @property string connectTime
 * @property string preTransferTime
 * @property string sizeUpload
 * @property string sizeDownload
 * @property string speedDownload
 * @property string speedUpload
 * @property string downloadContentLength
 * @property string uploadContentLength
 * @property string startTransferTime
 * @property string redirectTime
 * @property string redirectUrl
 * @property string primaryIp
 * @property string certInfo
 * @property string primaryPort
 * @property string localIp
 * @property string localPort
 */
class CurlInfo extends BaseObject
{
    
    /**
     * @var array
     */
    private $_info;
    
    /**
     * 字段对应表
     * @var array
     */
    private $_fieldWord = [
        'url'                   => 'url',
        'contentType'           => 'content_type',
        'httpCode'              => 'http_code',
        'headerSize'            => 'header_size',
        'requestSize'           => 'request_size',
        'fileTime'              => 'filetime',
        'sslVerifyResult'       => 'ssl_verify_result',
        'redirectCount'         => 'redirect_count',
        'totalTime'             => 'total_time',
        'nameLookupTime'        => 'namelookup_time',
        'connectTime'           => 'connect_time',
        'preTransferTime'       => 'pretransfer_time',
        'sizeUpload'            => 'size_upload',
        'sizeDownload'          => 'size_download',
        'speedDownload'         => 'speed_download',
        'speedUpload'           => 'speed_upload',
        'downloadContentLength' => 'download_content_length',
        'uploadContentLength'   => 'upload_content_length',
        'startTransferTime'     => 'starttransfer_time',
        'redirectTime'          => 'redirect_time',
        'redirectUrl'           => 'redirect_url',
        'primaryIp'             => 'primary_ip',
        'certInfo'              => 'certinfo',
        'primaryPort'           => 'primary_port',
        'localIp'               => 'local_ip',
        'localPort'             => 'local_port',
    ];
    
    /**
     * CurlInfo constructor.
     * @param $info
     */
    public function __construct($info = [])
    {
        parent::__construct([]);
        $this->_info = $info;
    }
    
    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
    
    /**
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        try {
            if ($value = parent::__get($name)) {
                return $value;
            }
        } catch (\Exception $e) {
        
        }
        if (isset($this->_fieldWord[$name])) {
            return $this->_info[$this->_fieldWord[$name]];
        }
        return null;
    }
    
    /**
     * @return array
     */
    public function all()
    {
        return $this->_info;
    }
    
    /**
     * 获取返回数据类型
     */
    public function getContentType()
    {
        $contentType = $this->_info['content_type'];
        
        if (strstr($contentType, '/json') !== false) {
            return 'json';
        }
        
        if (strstr($contentType, 'xml') !== false) {
            return 'xml';
        }
        return $contentType;
    }
    
}