<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\Helper;
use doyzheng\weixin\base\Result;

/**
 * Class Template
 * @package doyzheng\weixin\mp
 * @link    https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
 */
class Template extends Base
{
    
    // 设置所属行业
    const API_URL_SET_INDUSTRY = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=';
    // 获取设置的行业信息
    const API_URL_GET_INDUSTRY = 'https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=';
    // 添加模板ID
    const API_URL_ADD_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=';
    // 获取模板列表
    const API_URL_GET_ALL_PRIVATE_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=';
    // 删除模板
    const API_URL_DEL_PRIVATE_TEMPLATE = 'https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=';
    // 发送模板消息
    const API_URL_SEND = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=';
    
    /**
     * 设置所属行业
     * @param string $industryId1
     * @param string $industryId2
     * @return Result
     */
    public function setIndustry($industryId1, $industryId2)
    {
        $url    = self::API_URL_SET_INDUSTRY . $this->getAccessToken();
        $params = [
            'industry_id1' => $industryId1,
            'industry_id2' => $industryId2,
        ];
        return $this->api($url, $params);
    }
    
    /**
     * 获取设置的行业信息
     * @return Result
     */
    public function getIndustry()
    {
        $url = self::API_URL_GET_INDUSTRY . $this->getAccessToken();
        return $this->api($url, [], 'GET');
    }
    
    /**
     * 添加模板
     * @param string $templateIdShort
     * @return Result
     */
    public function addTemplate($templateIdShort)
    {
        $url    = self::API_URL_ADD_TEMPLATE . $this->getAccessToken();
        $params = [
            'template_id_short' => $templateIdShort,
        ];
        return $this->api($url, $params);
    }
    
    /**
     * 获取模板列表
     * @return Result
     */
    public function getAllPrivateTemplate()
    {
        $url = self::API_URL_GET_ALL_PRIVATE_TEMPLATE . $this->getAccessToken();
        return $this->api($url, [], 'GET');
    }
    
    /**
     * 删除模板
     * @param string $templateId
     * @return Result
     */
    public function delPrivateTemplate($templateId)
    {
        $url    = self::API_URL_DEL_PRIVATE_TEMPLATE . $this->getAccessToken();
        $params = [
            'template_id' => $templateId,
        ];
        return $this->api($url, $params);
    }
    
    /**
     * 发送模板消息
     * @param string $openid
     * @param string $templateId
     * @param string $url
     * @param array  $data
     * @param array  $extra
     * @return Result
     */
    public function send($openid, $templateId, $url, $data, $extra = [])
    {
        $params = Helper::arrayMerge([
            'touser'      => $openid,
            'template_id' => $templateId,
            'url'         => $url,
            'miniprogram' => [],
            'data'        => $data,
        ], $extra);
        return $this->api(self::API_URL_SEND . $this->getAccessToken(), $params);
    }
    
    /**
     * @param string $url
     * @param array  $params
     * @param string $method
     * @return Result
     */
    private function api($url, $params, $method = 'POST')
    {
        if ($method == 'POST') {
            $result = $this->app->request->postJson($url, $params);
        } else {
            $result = $this->app->request->get($url, $params);
        }
        if ($result->errmsg && $result->errcode) {
            $this->app->exception->request($result->errmsg, $result->errcode);
        }
        return $result;
    }
    
}
