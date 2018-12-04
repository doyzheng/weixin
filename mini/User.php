<?php

namespace doyzheng\weixin\mini;

/**
 * Class User
 * @package doyzheng\weixin\mini
 */
class User extends Base
{
    
    /**
     * @var string
     */
    private $apiUrl = "https://api.weixin.qq.com/sns/jscode2session";
    
    /**
     * 登录凭证校验，通过 wx.login() 接口获得临时登录凭证 code 后传到开发者服务器调用此接口完成登录流程。更多使用方法详见 小程序登录。
     * https://developers.weixin.qq.com/miniprogram/dev/api/open-api/login/code2Session.html?search-key=jscode2session
     * @param $code
     * @return array
     */
    public function code2session($code)
    {
        $params = [
            'appid'      => $this->app->accessToken->appid,
            'secret'     => $this->app->accessToken->secret,
            'js_code'    => $code,
            'grant_type' => 'authorization_code',
        ];
        $result = $this->app->request->get($this->apiUrl, $params);
        if ($result->errMsg && $result->errCode) {
            return $this->app->exception->request($result->errMsg, $result->errCode);
        }
        return $result->data();
    }
    
}
