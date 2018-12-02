<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\Helper;
use doyzheng\weixin\base\Result;

/**
 * 网页应用用户授权
 * Class Auth
 * @package doyzheng\weixin\mp
 */
class Auth extends Module
{
    
    /**
     * @var string 回调状态码
     */
    public $state = 'doyzhengWeixinAuthState';
    
    // 授权跳转接口
    const API_AUTHORIZE = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    // 获取token接口
    const API_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    // 获取用户信息接口
    const API_USER_INFO = 'https://api.weixin.qq.com/sns/userinfo';
    // 刷新Token接口
    const API_REFRESH_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/refresh_token/Wechat';
    
    /**
     *  第一步：用户同意授权，获取code
     * @param bool   $isUserInfo
     * @param string $state
     * @return string
     */
    public function getCode($isUserInfo = false, $state = '')
    {
        if (isset($_GET['code']) && isset($_GET['state']) && $_GET['code'] && $_GET['state'] == $state) {
            return $_GET['code'];
        } else {
            $query = [
                'appid'         => $this->app->accessToken->appid,
                'redirect_uri'  => Helper::getSelfUrl(),
                'response_type' => 'code',
                'scope'         => $isUserInfo ? 'snsapi_userinfo' : 'snsapi_base',
                'state'         => $state,
            ];
            $url   = self::API_AUTHORIZE . '?' . http_build_query($query) . '#wechat_redirect';
            exit(header('location: ' . $url));
        }
    }
    
    /**
     * 第二步：通过code换取网页授权access_token
     * @param bool $isUserInfo
     * @return array|mixed
     */
    public function getToken($isUserInfo = false)
    {
        $code = $this->getCode($isUserInfo, $this->state);
        if (empty($code)) {
            return $this->app->exception->error('获取code失败');
        }
        return $this->getTokenByCode($code);
    }
    
    /**
     * 第三步：刷新access_token（如果需要）
     * @param $refreshToken
     * @return array|mixed
     */
    public function refreshToken($refreshToken)
    {
        $query = [
            'appid'         => $this->accessToken->appid,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken
        ];
        return $this->api(self::API_REFRESH_TOKEN, $query);
    }
    
    /**
     * 第四步：拉取用户信息(需scope为 snsapi_userinfo)
     * @param bool $isUserInfo
     * @return array|mixed
     */
    public function getUserInfo($isUserInfo = false)
    {
        $result = $this->getToken($isUserInfo);
        $query  = [
            'access_token' => $result['access_token'],
            'openid'       => $result['openid'],
            'lang'         => 'zh_CN'
        ];
        return $this->api(self::API_USER_INFO, $query);
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
     * 统一调用接口方法
     * @param $url
     * @param $query
     * @return array
     */
    private function api($url, $query)
    {
        $result = $this->app->request->get($url, $query);
        if ($result->errCode && $result->errMsg) {
            return $this->app->exception->request($result->errMsg, $result->errCode);
        }
        return $result->data();
    }
    
}
