<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\BaseWeixin;
use doyzheng\weixin\core\Request;

/**
 * 网页应用用户授权
 * Class Auth
 * @package doyzheng\weixin\mp
 */
class Auth extends BaseWeixin
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
        if (empty($_GET['code']) && empty($_GET['state'])) {
            $query = [
                'appid'         => $this->accessToken->appid,
                'redirect_uri'  => Request::getSelfUrl(),
                'response_type' => 'code',
                'scope'         => $isUserInfo ? 'snsapi_userinfo' : 'snsapi_base',
                'state'         => $state,
            ];
            $url   = self::API_AUTHORIZE . '?' . http_build_query($query) . '#wechat_redirect';
            exit(header('location: ' . $url));
        }
        if ($_GET['state'] == $state) {
            return $_GET['code'];
        }
        return '';
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
            return $this->exception->logic('获取code失败');
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
     * 重定向
     */
    public function redirect($url = '')
    {
        if (!$url) {
            $protocol = 'http://';
            if ((!empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS'])
                || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                $protocol = 'https://';
            }
            $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            
            if (isset($_GET['code']) && isset($_GET['state'])) {
                $query = [
                    'code'  => $_GET['code'],
                    'state' => $_GET['state']
                ];
                $url = str_replace(http_build_query($query), '', $url);
            }
        }
        exit(header('location: ' . $url));
    }
    
    /**
     * 通过code换token
     * @param $code
     * @return array|mixed
     */
    public function getTokenByCode($code)
    {
        $query = [
            'appid'      => $this->accessToken->appid,
            'secret'     => $this->accessToken->secret,
            'code'       => $code,
            'grant_type' => 'authorization_code',
        ];
        return $this->api(self::API_ACCESS_TOKEN, $query);
    }
    
    /**
     * @param $url
     * @param $query
     * @return array|mixed
     */
    private function api($url, $query)
    {
        $result = $this->request->getJson($url, $query);
        if (isset($result['errcode']) && $result['errcode'] != '0') {
            return $this->exception->error($result['errmsg'], $result['errcode']);
        }
        return $result;
    }
    
}
