<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\BaseWeixin;
use doyzheng\weixin\core\Helper;

/**
 * Class Message
 * @package doyzheng\weixin\mp
 */
class Message extends BaseWeixin
{
    
    // 文本消息
    const MSG_TYPE_TEXT = 'text';
    // 图片消息
    const MSG_TYPE_IMAGE = 'image';
    // 语音消息
    const MSG_TYPE_VOICE = 'voice';
    // 视频消息
    const MSG_TYPE_VIDEO = 'video';
    // 音乐消息
    const MSG_TYPE_MUSIC = 'music';
    // 图文消息
    const MSG_TYPE_NEWS = 'news';
    // 获取位置事件
    const EVENT_TYPE_LOCATION = 'LOCATION';
    // 点击菜单事件
    const EVENT_TYPE_CLICK = 'CLICK';
    
    /**
     * 记录微信通知数据
     */
    protected function init()
    {
        $this->log->access('weixin_message');
    }
    
    /**
     * 接收普通文本
     * @param $type
     * @param $callback
     * @return bool|mixed
     */
    public function get($type, $callback)
    {
        $data = $this->encodeXmlData();
        
        if (empty($data['FromUserName']) || empty($data['FromUserName']) || empty($data['MsgType'])) {
            return false;
        }
        
        if ($data['MsgType'] == $type) {
            $receive = new Receive();
            $receive->setToUserName($data['FromUserName'])->setFromUserName($data['ToUserName']);
            return call_user_func_array($callback, [$receive, $data]);
        }
        return false;
    }
    
    /**
     * 事件消息
     * @param $type
     * @param $callback
     * @return bool|mixed
     */
    public function event($type, $callback)
    {
        $data = $this->encodeXmlData();
        
        if (empty($data['FromUserName']) || empty($data['FromUserName']) || empty($data['MsgType'])) {
            return false;
        }
        
        if ($data['MsgType'] == 'event' && $data['Event'] == $type) {
            $receive = new Receive();
            $receive->setToUserName($data['FromUserName'])->setFromUserName($data['ToUserName']);
            return call_user_func_array($callback, [$receive, $data]);
        }
        return false;
    }
    
    /**
     * 解析回复数据
     * @return array
     */
    public function encodeXmlData()
    {
        if (isset($_GET['echostr'])) {
            exit($_GET['echostr']);
        }
        $raw = file_get_contents('php://input');
        return Helper::xml2array($raw);
    }
    
}

