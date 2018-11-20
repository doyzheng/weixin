<?php

namespace doyzheng\weixin\mini;

use doyzheng\weixin\base\BaseWeixin;

/**
 * Class Qrcode
 * 二维码/小程序码
 * @package doyzheng\weixin\mini
 */
class Qrcode extends BaseWeixin
{
    
    /**
     * @var array 获取小程序码，适用于需要的码数量极多的业务场景。通过该接口生成的小程序码，永久有效，数量暂无限制。 更多用法详见 获取二维码。
     */
    private $apiUrls = [
        'getwxacodeunlimit' => 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=',
        'getwxacode'        => 'https://api.weixin.qq.com/wxa/getwxacode?access_token=',
        'createwxaqrcode'   => 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token='
    ];
    
    /**
     * 生成一个不限制的小程序码
     * @param string $scene
     * @param string $page
     * @param array  $extra
     * @return array|mixed
     */
    public function getWXACodeUnLimit($scene, $page, $extra = [])
    {
        $params = array_merge([
            'scene' => $scene,
            'page'  => $page
        ], $extra);
        
        return $this->api($this->apiUrls['getwxacodeunlimit'] . $this->accessToken, $params);
    }
    
    /**
     * 获取小程序码，适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制，详见获取二维码。
     * @param string $path
     * @param array  $extra
     * @return mixed
     */
    public function getWXACode($path, $extra = [])
    {
        $params = array_merge([
            'path' => $path
        ], $extra);
        
        return $this->api($this->apiUrls['getwxacode'] . $this->accessToken, $params);
    }
    
    /**
     * 获取小程序二维码，适用于需要的码数量较少的业务场景。通过该接口生成的小程序码，永久有效，有数量限制，详见获取二维码。
     * @param string     $path   扫码进入的小程序页面路径，最大长度 128 字节，不能为空
     * @param string|int $number 二维码的宽度，默认 430px
     * @return array|mixed
     */
    public function createWXAQRCode($path, $number = '430')
    {
        $params = [
            'path'   => $path,
            'number' => $number,
        ];
        return $this->api($this->apiUrls['createwxaqrcode'] . $this->accessToken, $params);
    }
    
    /**
     * @param $url
     * @param $params
     * @return array|mixed
     */
    private function api($url, $params)
    {
        $result = $this->request->postJson($url, $params);
        
        $data = $result->parseJson();
        if (isset($data['errcode']) && $data['errcode'] != '0') {
            return $this->exception->error($result['errmsg'], $result['errcode']);
        }
        
        return $data ? $data : $result->content;
    }
    
}
