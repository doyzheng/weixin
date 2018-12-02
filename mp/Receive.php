<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\Helper;

/**
 * 被动回复用户消息
 * Class Receive
 * @package doyzheng\weixin\mp
 */
class Receive extends Module
{
    
    /**
     * @var string
     */
    private $toUserName;
    
    /**
     * @var string
     */
    private $fromUserName;
    
    /**
     * 设置接收方帐号（收到的OpenID）
     * @param $openid
     * @return $this
     */
    public function setToUserName($openid)
    {
        $this->toUserName = $openid;
        return $this;
    }
    
    /**
     * 获取设置的接收方帐号（收到的OpenID）
     * @return string
     */
    public function getToUserName()
    {
        return $this->toUserName;
    }
    
    /**
     * 设置开发者微信号
     * @param $openid
     * @return $this
     */
    public function setFromUserName($openid)
    {
        $this->fromUserName = $openid;
        return $this;
    }
    
    /**
     * 获取设置的开发者微信号
     * @return string
     */
    public function getFromUserName()
    {
        return $this->fromUserName;
    }
    
    /**
     * 回复文本消息
     * @param string $content
     * @return bool|string
     */
    public function text($content)
    {
        $params = [
            'Content' => $content,
        ];
        return $this->make($params, Message::MSG_TYPE_TEXT);
    }
    
    /**
     * 回复图片消息
     * @param string $mediaId
     * @return bool|string
     */
    public function image($mediaId)
    {
        $params = [
            'MediaId' => $mediaId,
        ];
        return $this->make($params, Message::MSG_TYPE_IMAGE);
    }
    
    /**
     * 回复语音消息
     * @param string $mediaId
     * @return bool|string
     */
    public function voice($mediaId)
    {
        $params = [
            'MediaId' => $mediaId,
        ];
        return $this->make($params, Message::MSG_TYPE_VOICE);
    }
    
    /**
     * 回复视频消息
     * @param string $mediaId
     * @param string $title
     * @param string $description
     * @return bool|string
     */
    public function video($mediaId, $title = '', $description = '')
    {
        $params = [
            'MediaId'     => $mediaId,
            'Title'       => $title,
            'Description' => $description,
        ];
        return $this->make($params, Message::MSG_TYPE_VIDEO);
    }
    
    /**
     * 回复音乐消息
     * @param string $thumbMediaId
     * @param string $title
     * @param string $description
     * @param string $musicURL
     * @param string $HQMusicUrl
     * @return bool|string
     */
    public function music($thumbMediaId, $title = '', $description = '', $musicURL = '', $HQMusicUrl = '')
    {
        $params = [
            'ThumbMediaId' => $thumbMediaId,
            'Title'        => $title,
            'Description'  => $description,
            'MusicURL'     => $musicURL,
            'HQMusicUrl'   => $HQMusicUrl,
        ];
        return $this->make($params, Message::MSG_TYPE_MUSIC);
    }
    
    /**
     * 回复图文消息
     * @param $title
     * @param $description
     * @param $picUrl
     * @param $url
     * @return bool|string
     */
    public function news($title, $description, $picUrl, $url)
    {
        $params = [
            'ArticleCount' => '1',
            'Articles'     => [
                'Title'       => $title,
                'Description' => $description,
                'PicUrl'      => $picUrl,
                'Url'         => $url,
            ]
        ];
        return $this->make($params, Message::MSG_TYPE_NEWS);
    }
    
    /**
     * 构造回复数据
     * @param array  $params
     * @param string $type
     * @return bool|string
     */
    private function make($params, $type)
    {
        $data = Helper::arrayMerge([
            'ToUserName'   => $this->toUserName,
            'FromUserName' => $this->fromUserName,
            'CreateTime'   => time(),
            'MsgType'      => $type,
        ], $params);
        
        if (empty($data['ToUserName'])) {
            return $this->app->exception->invalidArgument('接收方帐号（收到的OpenID）不能为空: ToUserName');
        }
        
        if (empty($data['FromUserName'])) {
            return $this->app->exception->invalidArgument('开发者微信号不能为空: FromUserName');
        }
        
        return Helper::array2xml($data);
    }
    
}
