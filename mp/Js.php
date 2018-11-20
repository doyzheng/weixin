<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\BaseWeixin;
use doyzheng\weixin\core\Helper;
use doyzheng\weixin\core\Request;

/**
 * 微信JsApi
 * @package doyzheng\weixin\mp
 */
class Js extends BaseWeixin
{
    
    /**
     * @var string
     */
    protected $url;
    
    /**
     * Ticket cache prefix.
     * @var string
     */
    protected $cachePrefix = 'doyzheng.weixin.jsapi_ticket.';
    
    /**
     * Api of ticket.
     */
    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=';
    
    /**
     * 获取jsApi配置
     * @param array $APIs
     * @param bool  $debug
     * @param bool  $beta
     * @param bool  $json
     * @return array|string
     */
    public function config(array $APIs, $debug = false, $beta = false, $json = true)
    {
        $signPackage = $this->signature();
        
        $base   = [
            'debug' => $debug,
            'beta'  => $beta,
        ];
        $config = array_merge($base, $signPackage, ['jsApiList' => $APIs]);
        
        return $json ? json_encode($config) : $config;
    }
    
    /**
     * 获取jsApi配置
     * @param array $APIs
     * @param bool  $debug
     * @param bool  $beta
     * @return array|string
     */
    public function getConfigArray(array $APIs = [], $debug = false, $beta = false)
    {
        return $this->config($APIs, $debug, $beta, false);
    }
    
    /**
     * 获取jsApi ticket
     * @param bool $forceRefresh
     * @return mixed
     */
    public function ticket($forceRefresh = false)
    {
        $key = $this->cachePrefix . $this->accessToken->appid;
        
        $ticket = $this->cache->get($key);
        
        if (!$forceRefresh && !empty($ticket)) {
            return $ticket;
        }
        
        $result = $this->request->getJson(self::API_TICKET . $this->accessToken, [
            'type' => 'jsapi'
        ]);
        
        if (isset($result['errcode']) && $result['errcode'] != '0' && isset($result['errmsg'])) {
            $this->exception->error($result['errmsg'], $result['errcode']);
        }
        
        $this->cache->set($key, $result['ticket'], $result['expires_in'] - 500);
        return $result['ticket'];
    }
    
    /**
     * 构造签名
     * @param string $url
     * @param string $nonce
     * @param int    $timestamp
     * @return array
     */
    public function signature($url = null, $nonce = null, $timestamp = null)
    {
        $url       = $url ? $url : $this->getUrl();
        $nonce     = $nonce ? $nonce : Helper::generateRandStr(10);
        $timestamp = $timestamp ? $timestamp : time();
        $ticket    = $this->ticket();
        $sign      = [
            'appId'     => $this->accessToken->appid,
            'nonceStr'  => $nonce,
            'timestamp' => $timestamp,
            'url'       => $url,
            'signature' => $this->getSignature($ticket, $nonce, $timestamp, $url),
        ];
        return $sign;
    }
    
    /**
     * 拼接签名字符串参数
     * @param string $ticket
     * @param string $nonce
     * @param string $timestamp
     * @param string $url
     * @return string
     */
    public function getSignature($ticket, $nonce, $timestamp, $url)
    {
        return sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }
    
    /**
     * 设置使用jsApi页面Url
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        
        return $this;
    }
    
    /**
     * 获取已经设置的jsApi页面Url
     * @return string
     */
    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }
        return Request::getSelfUrl();
    }
    
}
