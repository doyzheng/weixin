<?php

namespace doyzheng\weixin\core;

use doyzheng\weixin\base\BaseWeixin;

/**
 * Class AccessToken
 * @package doyzheng\weixin\core
 */
class AccessToken extends BaseWeixin
{
    
    /**
     * @var string
     */
    public $appid;
    
    /**
     * @var string
     */
    public $secret;
    
    /**
     * @var int 失效时长
     */
    public $duration = 7000;
    
    /**
     * @var string 缓存键前缀
     */
    public $keyPrefix = 'doyzheng.weixin.accesss_token.';
    
    /**
     * @var string Api接口地址
     */
    private $url = 'https://api.weixin.qq.com/cgi-bin/token';
    
    /**
     * Module constructor.
     * @param array $config
     */
    function __construct($config = [])
    {
        if (empty($config['appid'])) {
            $this->exception->invalidArgument('appid');
        }
        
        if (empty($config['secret'])) {
            $this->exception->invalidArgument('secret');
        }
        
        parent::__construct($config);
    }
    
    /**
     * @return request\Result|null
     */
    private function getTokenByService()
    {
        $params = [
            'appid'      => $this->appid,
            'secret'     => $this->secret,
            'grant_type' => 'client_credential',
        ];
        return $this->request->get($this->url, $params);
    }
    
    /**
     * 获取访问token
     * @param bool $isRefresh
     * @return mixed
     */
    public function getToken($isRefresh = false)
    {
        $key = $this->keyPrefix . $this->appid;
        
        $cache = $this->cache;
        if (($token = $cache->get($key)) == null || $isRefresh) {
            $result = $this->getTokenByService();
            
            $data = $result->parseJson();
            
            if (isset($data['errcode']) && $data['errcode'] != '0') {
                $this->exception->error($data['errmsg'], $data['errcode']);
            }
            
            $token = $data['access_token'];
            $cache->set($key, $token, $this->duration);
        }
        return $token;
    }
    
    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->getToken();
    }
    
}
