<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\Helper;

/**
 * 临时素材
 * Class Media
 * @package doyzheng\weixin\mp
 * @link    https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738726
 */
class Media extends Base
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
        $url    = self::API_GET . $this->app->accessToken;
        $result = $this->app->request->get($url, ['media_id' => $mediaId]);
        if ($result->errmsg && $result->errcode) {
            return $this->app->exception->request($result->errmsg, $result->errcode);
        }
        return $result;
    }
    
    /**
     * 上传临时素材
     * @param string $filename 文件名
     * @param string $type     素材类型
     * @return array
     */
    private function upload($filename, $type)
    {
        // 支持远程图片上传
        if (preg_match("/^http(.*)/", $filename)) {
            $tmpFile = $filename = $this->downloadRemoteFile($filename);
        }
        if (!is_file($filename)) {
            return $this->app->exception->error('文件不存在: ' . $filename);
        }
        $query  = http_build_query([
            'access_token' => $this->app->accessToken->getToken(),
            'type'         => $type
        ]);
        $data   = [
            'media' => new \CURLFile($filename)
        ];
        $result = $this->app->request->post(self::API_UPLOAD . $query, $data);
        // 如果是远程图片上传后直接删除
        if (isset($tmpFile) && is_file($tmpFile)) {
            @unlink($tmpFile);
        }
        if ($result->errmsg && $result->errcode) {
            return $this->app->exception->request($result->errmsg, $result->errcode);
        }
        return $result->data();
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
    
    /**
     * 返回临时文件Url
     * @param string $mediaId
     * @return string
     */
    public function mediaId2Url($mediaId)
    {
        if (!$mediaId) {
            return '';
        }
        if (is_array($mediaId) && isset($mediaId['media_id'])) {
            $mediaId = $mediaId['media_id'];
        }
        return "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$this->app->accessToken}&media_id=" . $mediaId;
    }
    
    /**
     * 下载远程文件
     * @param string $url
     * @param string $filename
     * @return string
     */
    public function downloadRemoteFile($url, $filename = '')
    {
        $extList     = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png'
        ];
        $result      = $this->app->request->get($url);
        $contentType = $result->info->contentType;
        if (empty($extList[$contentType])) {
            $this->app->exception->error("Unsupported download file type $contentType");
        }
        if (!$filename) {
            $filename = Helper::mkdir($this->app->runtimePath . '/temp/') . md5($filename . rand()) . '.' . $extList[$contentType];
        }
        if (@file_put_contents($filename, $result->content)) {
            return $filename;
        }
        return '';
    }
    
}
