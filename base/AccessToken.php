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
        parent::init();
    }

    /**
     * 获取api访问token
     * @param bool $isRefresh
     * @return string
     */
    public function getToken($isRefresh = false)
    {
        if (!$this->accessToken) {
            $key   = $this->keyPrefix . $this->appid;
            $cache = $this->app->cache;
            if (($this->accessToken = $cache->get($key)) == null || $isRefresh) {
                $result = $this->getTokenByService();
                if ($result->errCode && $result->errMsg) {
                    $this->app->exception->error($result->errMsg, $result->errCode);
                }
                $this->accessToken = $result->data('access_token');
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
     * @return Result|mixed
     */
    private function getTokenByService()
    {
        $params = [
            'appid'      => $this->appid,
            'secret'     => $this->secret,
            'grant_type' => 'client_credential',
        ];
        return $this->app->request->get($this->url, $params);
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