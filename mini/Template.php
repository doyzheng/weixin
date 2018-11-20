<?php

namespace doyzheng\weixin\mini;

use doyzheng\weixin\base\BaseWeixin;

/**
 * Class Template
 * @package doyzheng\weixin\mini
 */
class Template extends BaseWeixin
{
    
    /**
     * @var string 接口地址
     */
    private $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=';
    
    /**
     * 发送模板消息
     * @param string $openid
     * @param string $templateId
     * @param string $formId
     * @param array  $data
     * @param array  $extra
     * @return bool
     */
    public function send($openid, $templateId, $formId, $data, $extra = [])
    {
        $url    = $this->url . $this->accessToken;
        $params = array_merge($extra, [
            'touser'      => $openid,
            'template_id' => $templateId,
            'form_id'     => $formId,
            'data'        => $data,
        ]);
        $result = $this->request->postJson($url, $params);
        if (isset($result['errcode']) && $result['errcode'] != '0') {
            return $this->exception->error($result['errmsg'], $result['errcode']);
        }
        return true;
    }
    
}
