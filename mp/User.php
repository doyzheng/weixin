<?php

namespace doyzheng\weixin\mp;

/**
 * 用户管理
 * Class User
 * @package doyzheng\weixin\mp
 *
 */
class User extends Module
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
    public function getInfo($openid)
    {
        $query = [
            'access_token' => $this->getAccessToken(),
            'openid'       => $openid,
            'lang'         => 'zh_CN'
        ];
        return $this->api($this->apiUrls['info'], $query);
    }
    
    /**
     * 批量获取用户信息
     * @param $openList
     * @return array|mixed
     */
    public function batchGet($openList)
    {
        $url    = $this->apiUrls['batchget'] . '?access_token=' . $this->getAccessToken();
        $params = [];
        foreach ($openList as $openid) {
            $params['user_list'][] = [
                'openid' => $openid,
                'lang'   => 'zh_CN',
            ];
        }
        return $this->api($url, $params, 'POST');
    }
    
    /**
     * 设置用户备注名
     * @param $openid
     * @param $remark
     * @return array|mixed
     */
    public function updateRemark($openid, $remark)
    {
        $url    = $this->apiUrls['updateremark'] . '?access_token=' . $this->getAccessToken();
        $params = [
            'openid' => $openid,
            'remark' => $remark
        ];
        return $this->api($url, $params, 'POST');
    }
    
    /**
     * 获取用户列表
     * @param $nextOpenid
     * @return array|mixed
     */
    public function getList($nextOpenid = '')
    {
        $query = [
            'access_token' => $this->getAccessToken(),
            'next_openid'  => $nextOpenid,
        ];
        return $this->api($this->apiUrls['get'], $query);
    }
    
    /**
     * @param string $url
     * @param array  $params
     * @param string $method
     * @return array|mixed
     */
    private function api($url, $params, $method = 'GET')
    {
        if ($method == 'GET') {
            $result = $this->app->request->get($url, $params);
        } else {
            $result = $this->app->request->postJson($url, $params);
        }
        if ($result->errMsg && $result->errCode) {
            return $this->app->exception->request($result->errMsg, $result->errCode);
        }
        return $result->data();
    }
    
}
