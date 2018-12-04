<?php

namespace doyzheng\weixin\mini;

/**
 * Class Template
 * @package doyzheng\weixin\mini
 */
class Template extends Base
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
        $url    = $this->url . $this->getAccessToken();
        $params = array_merge($extra, [
            'touser'      => $openid,
            'template_id' => $templateId,
            'form_id'     => $formId,
            'data'        => $data,
        ]);
        $result = $this->app->request->postJson($url, $params);
        if ($result->errMsg && $result->errCode) {
            return $this->app->exception->request($result->errMsg, $result->errCode);
        }
        return true;
    }
    
}
