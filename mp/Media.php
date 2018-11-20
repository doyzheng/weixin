<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\BaseWeixin;

/**
 * 临时素材
 * Class Media
 * @package doyzheng\weixin\mp
 * @link    https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738726
 */
class Media extends BaseWeixin
{
    
    /**
     * @var string 图片类型
     */
    const FILE_TYPE_IMAGE = 'image';
    /**
     * @var string 语音类型
     */
    const FILE_TYPE_VOICE = 'voice';
    /**
     * @var string 视频类型
     */
    const FILE_TYPE_VIDEO = 'video';
    /**
     * @var string 缩略图（thumb，主要用于视频与音乐格式的缩略图）
     */
    const FILE_TYPE_THUMB = 'thumb';
    
    // 新增临时素材
    const API_UPLOAD = 'https://api.weixin.qq.com/cgi-bin/media/upload?';
    // 获取临时素材
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=';
    
    /**
     * 获取临时素材
     * @param $mediaId
     * @return string
     */
    public function get($mediaId)
    {
        $url = self::API_GET . $this->accessToken;
        $result = $this->request->get($url, ['media_id' => $mediaId]);
        if ($data = $result->parseJson()) {
            if (isset($data['errcode']) && $data['errcode'] != '0') {
                return $this->exception->error($data['errmsg'], $data['errcode']);
            }
        }
        return $result->content;
    }
    
    /**
     * 上传临时素材
     * @param string $filename 文件名
     * @param string $type     素材类型
     * @return array|string
     */
    private function upload($filename, $type)
    {
        if (!is_file($filename)) {
            return $this->exception->logic('文件不存在: ' . $filename);
        }
        $query  = http_build_query([
            'access_token' => $this->accessToken->getToken(),
            'type'         => $type
        ]);
        $data   = [
            'media' => new \CURLFile($filename)
        ];
        $result = $this->request->post(self::API_UPLOAD . $query, $data);
        if ($data = $result->parseJson()) {
            if (isset($data['errcode']) && $data['errcode'] != '0') {
                return $this->exception->error($data['errmsg'], $data['errcode']);
            }
            return $data;
        }
        return $result->content;
    }
    
    /**
     * 上传临时图片素材
     * @param string $filename
     * @return array
     */
    public function uploadImage($filename)
    {
        return $this->upload($filename, self::FILE_TYPE_IMAGE);
    }
    
    /**
     * 上传临时语音素材
     * @param string $filename
     * @return array
     */
    public function uploadVoice($filename)
    {
        return $this->upload($filename, self::FILE_TYPE_VOICE);
    }
    
    /**
     * 上传临时视频素材
     * @param string $filename
     * @return array
     */
    public function uploadVideo($filename)
    {
        return $this->upload($filename, self::FILE_TYPE_VIDEO);
    }
    
    /**
     * 上传临时图片素材
     * @param string $filename
     * @return array
     */
    public function uploadThumb($filename)
    {
        return $this->upload($filename, self::FILE_TYPE_THUMB);
    }
    
}
