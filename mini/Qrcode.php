<?php

namespace doyzheng\weixin\mini;

/**
 * Class Qrcode
 * 二维码/小程序码
 * @package doyzheng\weixin\mini
 */
class Qrcode extends Base
{
    
    const API_URL_GET = 'https://api.weixin.qq.com/wxa/getwxacode?access_token=';
    const API_URL_GET_LIMIT = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=';
    const API_URL_CREATE = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=';
    
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
        $url    = self::API_URL_GET_LIMIT . $this->getAccessToken();
        return $this->api($url, $params);
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
        $url    = self::API_URL_GET . $this->getAccessToken();
        return $this->api($url, $params);
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
        $url    = self::API_URL_CREATE . $this->getAccessToken();
        return $this->api($url, $params);
    }
    
    /**
     * @param $url
     * @param $params
     * @return array|mixed
     */
    private function api($url, $params)
    {
        $result = $this->app->request->post($url, $params);
        if ($result->errMsg && $result->errCode) {
            return $this->app->exception->request($result->errMsg, $result->errCode);
        }
        return $result->content;
    }
    
}
