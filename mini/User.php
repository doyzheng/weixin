<?php

namespace doyzheng\weixin\mini;

use doyzheng\weixin\base\BaseWeixin;

/**
 * Class User
 * @package doyzheng\weixin\mini
 */
class User extends BaseWeixin
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
    public function jscode2session($code)
    {
        $params = [
            'appid'      => $this->accessToken->appid,
            'secret'     => $this->accessToken->secret,
            'js_code'    => $code,
            'grant_type' => 'authorization_code',
        ];
        $result = $this->request->getJson($this->apiUrl, $params);
        if (!empty($result['openid']) && !empty($result['session_key'])) {
            return $result;
        }
        if (isset($result['errcode']) && $result['errcode'] != '0') {
            return $this->exception->error($result['errmsg'], $result['errcode']);
        }
        return $this->exception->error('获取session_key失败');
    }
    
}
