<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\Helper;
use doyzheng\weixin\base\Result;

/**
 * 网页应用用户授权
 * Class Auth
 * @package doyzheng\weixin\mp
 */
class Auth extends Base
{
    
    /**
     * @var string 回调状态码
     */
    private $state;
    
    /**
     * @var string 回调地址
     */
    private $redirectUri;
    
    // 授权跳转接口
    const API_AUTHORIZE = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    // 获取token接口
    const API_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    // 获取用户信息接口
    const API_USER_INFO = 'https://api.weixin.qq.com/sns/userinfo';
    // 刷新Token接口
    const API_REFRESH_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/refresh_token/Wechat';
    
    /**
     * 初始化赋值
     */
    protected function init()
    {
        $this->state       = 'doyzhengWeixinAuthState';
        $this->redirectUri = Helper::getSelfUrl();
    }
    
    /**
     * 用户同意授权，获取code
     * @param bool $isUserInfo
     * @return string
     */
    public function getCode($isUserInfo = false)
    {
        if (isset($_GET['code']) && isset($_GET['state']) && $_GET['code'] && $_GET['state'] == $this->state) {
            return $_GET['code'];
        } else {
            $query = [
                'appid'         => $this->app->accessToken->appid,
                'redirect_uri'  => $this->redirectUri,
                'response_type' => 'code',
                'scope'         => $isUserInfo ? 'snsapi_userinfo' : 'snsapi_base',
                'state'         => $this->state,
            ];
            $url   = self::API_AUTHORIZE . '?' . http_build_query($query) . '#wechat_redirect';
            exit(header('location: ' . $url));
        }
    }
    
    /**
     * 刷新access_token（如果需要）
     * @param $refreshToken
     * @return array|mixed
     */
    public function refreshToken($refreshToken)
    {
        $query = [
            'appid'         => $this->app->accessToken->appid,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken
        ];
        return $this->api(self::API_REFRESH_TOKEN, $query);
    }
    
    /**
     * 获取用户信息(需scope为 snsapi_userinfo)
     * @param bool $isUserInfo
     * @return Result
     */
    public function getUserInfo($isUserInfo = false)
    {
        $code   = $this->getCode($isUserInfo);
        $result = $this->getTokenByCode($code);
        if (!$result) {
            return $this->app->exception->error('code换获取access_token失败');
        }
        $query = [
            'access_token' => $result['access_token'],
            'openid'       => $result['openid'],
            'lang'         => 'zh_CN'
        ];
        return $this->api(self::API_USER_INFO, $query);
    }
    
    /**
     * 通过code换token
     * @param $code
     * @return array|mixed
     */
    public function getTokenByCode($code)
    {
        $query = [
            'appid'      => $this->app->accessToken->appid,
            'secret'     => $this->app->accessToken->secret,
            'code'       => $code,
            'grant_type' => 'authorization_code',
        ];
        return $this->api(self::API_ACCESS_TOKEN, $query);
    }
    
    /**
     * 重定向到来源页
     */
    public function redirect()
    {
        $arr = explode('?', Helper::getSelfUrl());
        exit(header('location: ' . array_shift($arr)));
    }
    
    /**
     * 设置回调状态码
     * @param $value
     * @return $this
     */
    public function setState($value)
    {
        $this->state = $value;
        return $this;
    }
    
    /**
     * 设置回调地址
     * @param $uri
     * @return $this
     */
    public function setRedirectUri($uri)
    {
        $this->redirectUri = $uri;
        return $this;
    }
    
    /**
     * 统一调用接口方法
     * @param $url
     * @param $query
     * @return Result
     */
    private function api($url, $query)
    {
        $result = $this->app->request->get($url, $query);
        if ($result->errmsg && $result->errcode) {
            return $this->app->exception->request($result->errmsg, $result->errcode);
        }
        return $result;
    }
    
}
