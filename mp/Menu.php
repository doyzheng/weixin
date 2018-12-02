<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\NotifyTrait;

/**
 * 自定义菜单
 * Class Menu
 * @package doyzheng\weixin\mp\js
 * @link    https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013
 */
class Menu extends Module
{
    
    // 点击菜单拉取消息时的事件推送
    const EVENT_CLICK = 'CLICK';
    // 点击菜单跳转链接时的事件推送
    const EVENT_VIEW = 'VIEW';
    // 扫码推事件的事件推送
    const EVENT_SCAN_CODE_PUSH = 'scancode_push';
    // 扫码推事件且弹出“消息接收中”提示框的事件推送
    const EVENT_SCAN_CODE_WAIT_MSG = 'scancode_waitmsg';
    // 弹出系统拍照发图的事件推送
    const EVENT_PIC_SYS_PHOTO = 'pic_sysphoto';
    // 弹出拍照或者相册发图的事件推送
    const EVENT_PIC_PHOTO_OR_ALBUM = 'pic_photo_or_album';
    // 弹出微信相册发图器的事件推送
    const EVENT_PIC_WEI_XIN = 'pic_weixin';
    // 弹出地理位置选择器的事件推送
    const EVENT_LOCATION_SELECT = 'location_select';
    
    // 创建菜单
    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=';
    // 查询菜单
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=';
    // 删除菜单
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=';
    
    use NotifyTrait;
    
    /**
     * 创建菜单
     * @param $button
     * @return array
     */
    public function create($button)
    {
        $url = self::API_CREATE . $this->getAccessToken();
        return $this->api($url, 'POST', ['button' => $button]);
    }
    
    /**
     * 查询菜单
     * @return array|mixed
     */
    public function get()
    {
        $url = self::API_GET . $this->getAccessToken();
        return $this->api($url);
    }
    
    /**
     * 自定义菜单删除接口
     * 使用接口创建自定义菜单后，开发者还可使用接口删除当前使用的自定义菜单。另请注意，在个性化菜单时，调用此接口会删除默认菜单及全部个性化菜单。
     * @return array|null
     */
    public function delete()
    {
        $url = self::API_DELETE . $this->getAccessToken();
        return $this->api($url);
    }
    
    /**
     * 自定义菜单事件推送
     * @param $event
     * @param $callback
     * @return bool|mixed
     */
    public function event($event, $callback)
    {
        $data = self::getRawDataXml();
        if (empty($data)) {
            return $this->app->exception->notify('事件推送消息数据为空');
        }
        if ($data['Event'] == $event) {
            return call_user_func_array($callback, [$data]);
        }
        return false;
    }
    
    /**
     * @param string $url
     * @param string $method
     * @param array  $params
     * @return array
     */
    public function api($url, $method = 'GET', $params = [])
    {
        if ($method == 'GET') {
            $result = $this->app->request->get($url);
        } else {
            $result = $this->app->request->post($url, $params);
        }
        if ($result->errMsg && $result->errCode) {
            return $this->app->exception->request($result->errMsg, $result->errCode);
        }
        return $result->data();
    }
    
}
