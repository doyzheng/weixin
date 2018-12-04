<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\base\interfaces\AccessTokenInterface;

/**
 * Class AccessToken
 * @package doyzheng\weixin\base
 */
class AccessToken extends BaseObject implements AccessTokenInterface
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
     * @var string token
     */
    private $accessToken;
    
    /**
     * 检测必要参数
     */
    public function init()
    {
        if (!$this->appid) {
            $this->app->exception->invalidArgument('公众账号不能为空: appid');
        }
        if (!$this->secret) {
            $this->app->exception->invalidArgument('公众账号秘钥不能为空: secret');
        }
    }
    
    /**
     * 获取api访问token(有缓存)
     * @param bool $isRefresh
     * @return string
     */
    public function getToken($isRefresh = false)
    {
        if (!$this->accessToken || $isRefresh) {
            $key   = $this->keyPrefix . $this->appid;
            $cache = $this->app->cache;
            // 缓存是否存在
            if (($this->accessToken = $cache->get($key)) == null || $isRefresh) {
                $this->accessToken = $this->getTokenByService();
                $cache->set($key, $this->accessToken, $this->duration);
            }
        }
        return $this->accessToken;
    }
    
    /**
     * 设置token
     * @param string $accessToken
     */
    public function setToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
    
    /**
     * 从服务器获取token
     * @return string
     */
    private function getTokenByService()
    {
        $params = [
            'appid'      => $this->appid,
            'secret'     => $this->secret,
            'grant_type' => 'client_credential',
        ];
        $result = $this->app->request->get($this->url, $params);
        if ($result->errmsg && $result->errcode) {
            $this->app->exception->request($result->errmsg, $result->errcode);
        }
        return $result->access_token;
    }
    
    /**
     * 当对象被当作字符串处理时返回token
     * @return mixed
     */
    public function __toString()
    {
        return $this->getToken();
    }
    
}