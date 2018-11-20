<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\BaseWeixin;

/**
 * 用户管理
 * Class User
 * @package doyzheng\weixin\mp
 *
 */
class User extends BaseWeixin
{
    
    /**
     * @var array 用户管理APi列表
     */
    private $apiUrls = [
        // 获取微信用户详细信息
        'info'         => 'https://api.weixin.qq.com/cgi-bin/user/info',
        // 批量获取用户信息
        'batchget'     => 'https://api.weixin.qq.com/cgi-bin/user/info/batchget',
        // 获取用户列表
        'get'          => 'https://api.weixin.qq.com/cgi-bin/user/get',
        // 设置用户备注名
        'updateremark' => 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark',
    ];
    
    /**
     * 获取微信用户详细信息
     * @param $openid
     * @return array
     */
    public function info($openid)
    {
        $query = [
            'access_token' => $this->accessToken->getToken(),
            'openid'       => $openid,
            'lang'         => 'zh_CN'
        ];
        return $this->api($this->apiUrls['info'], $query, 'GET');
    }
    
    /**
     * 批量获取用户信息
     * @param $openList
     * @return array|mixed
     */
    public function batchget($openList)
    {
        $url    = $this->apiUrls['batchget'] . '?access_token=' . $this->accessToken;
        $params = [];
        foreach ($openList as $openid) {
            $params['user_list'][] = [
                'openid' => $openid,
                'lang'   => 'zh_CN',
            ];
        }
        return $this->api($url, $params);
    }
    
    /**
     * 设置用户备注名
     * @param $openid
     * @param $remark
     * @return array|mixed
     */
    public function updateRemark($openid, $remark)
    {
        $url    = $this->apiUrls['updateremark'] . '?access_token=' . $this->accessToken;
        $params = [
            'openid' => $openid,
            'remark' => $remark
        ];
        return $this->api($url, $params);
    }
    
    /**
     * 获取用户列表
     * @param $nextOpenid
     * @return array|mixed
     */
    public function getList($nextOpenid = '')
    {
        $query = [
            'access_token' => $this->accessToken->getToken(),
            'next_openid'  => $nextOpenid,
        ];
        return $this->api($this->apiUrls['get'], $query, 'GET');
    }
    
    /**
     * @param string $url
     * @param array  $params
     * @param string $method
     * @return array|mixed
     */
    private function api($url, $params, $method = 'POST')
    {
        if ($method == 'GET') {
            $result = $this->request->getJson($url, $params);
        } else {
            $result = $this->request->postJson($url, $params)->parseJson();
        }
        if (isset($result['errcode']) && $result['errcode'] != '0') {
            return $this->exception->error($result['errmsg'], $result['errcode']);
        }
        return $result;
    }
    
}
