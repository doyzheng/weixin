<?php

namespace doyzheng\weixin\mp;

use doyzheng\weixin\base\Helper;
use doyzheng\weixin\base\Result;

/**
 * 永久素材
 * Class Material
 * @package doyzheng\weixin\mp
 * @link    https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738729
 */
class Material extends Base
{
    
    // 获取永久素材 GET
    const API_GET_MATERIAL = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=';
    // 获取素材总数 GET
    const API_GET_MATERIAL_COUNT = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount';
    // 新增其他类型永久素材  POST
    const API_ADD_MATERIAL = 'https://api.weixin.qq.com/cgi-bin/material/add_material';
    // 新增永久图文素材 POST
    const API_ADD_NEWS = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=';
    // 获取素材列表 POST
    const API_BATCH_GET_MATERIAL = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=';
    // 上传图文消息内的图片获取URL POST
    const API_UPLOAD_IMG = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=';
    // 删除永久素材 POST
    const API_DEL_MATERIAL = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=';
    // 修改永久图文素材 POST
    const API_UPDATE_NEWS = 'https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=';
    
    /**
     * 获取永久素材
     * @param $mediaId
     * @return Result
     */
    public function get($mediaId)
    {
        $url   = self::API_GET_MATERIAL . $this->getAccessToken();
        $query = [
            'media_id'     => $mediaId,
        ];
        return $this->api($url, $query);
    }
    
    /**
     * 新增永久图文素材
     * @param $params
     * @return Result
     */
    public function addNews($params)
    {
        $url           = self::API_ADD_NEWS . $this->getAccessToken();
        $defaultParams = [
            'title'              => '',
            'thumb_media_id'     => '',
            'show_cover_pic'     => '',
            'content'            => '',
            'content_source_url' => '',
            'author'             => false,
            'digest'             => false,
        ];
        $params        = Helper::arrayMerge($defaultParams, $params);
        return $this->api($url, $params);
    }
    
    /**
     * 获取素材总数
     * @return Result
     */
    public function getCount()
    {
        $url   = self::API_GET_MATERIAL_COUNT;
        $query = [
            'access_token' => $this->getAccessToken()
        ];
        return $this->api($url, $query, 'GET');
    }
    
    /**
     * 获取素材列表
     * @param string $type
     * @param string $offset
     * @param string $count
     * @return Result
     */
    public function getList($type = "image", $offset = '0', $count = '20')
    {
        $url    = self::API_BATCH_GET_MATERIAL . $this->getAccessToken();
        $params = [
            'type'   => $type,
            'offset' => $offset,
            'count'  => $count,
        ];
        return $this->api($url, $params);
    }
    
    /**
     * 删除永久素材
     * @param $mediaId
     * @return Result
     */
    public function del($mediaId)
    {
        $url    = self::API_DEL_MATERIAL . $this->getAccessToken();
        $params = [
            'media_id' => $mediaId,
        ];
        return $this->api($url, $params);
    }
    
    /**
     * 修改永久图文素材
     * @param $params
     * @return Result
     */
    public function updateNews($params)
    {
        $url           = self::API_UPDATE_NEWS . $this->getAccessToken();
        $defaultParams = [
            'media_id' => '',
            'index'    => '',
            'articles' => [
                'title'              => '',
                'thumb_media_id'     => '',
                'author'             => '',
                'digest'             => '',
                'show_cover_pic'     => '',
                'content'            => '',
                'content_source_url' => '',
            ],
        ];
        $params        = Helper::arrayMerge($defaultParams, $params);
        return $this->api($url, $params);
    }
    
    /**
     * @param string $url
     * @param array  $data
     * @param string $method
     * @return Result
     */
    private function api($url, $data, $method = 'POST')
    {
        if ($method == "POST") {
            $result = $this->app->request->postJson($url, $data);
        } else {
            $result = $this->app->request->get($url, $data);
        }
        if ($result->errMsg && $result->errCode) {
            return $this->app->exception->request($result->errMsg, $result->errCode);
        }
        return $result;
    }
    
    /**
     * 上传永久素材
     * @param string $filename
     * @param string $type
     * @param array  $params
     * @return Result
     */
    private function upload($filename, $type, $params = [])
    {
        if (!is_file($filename)) {
            return $this->app->exception->error('文件不存在: ' . $filename);
        }
        $query  = [
            'access_token' => $this->getAccessToken(),
            'type'         => $type,
        ];
        $url    = self::API_ADD_MATERIAL . '?' . http_build_query($query);
        $params = Helper::arrayMerge([
            'media' => new \CURLFile($filename),
        ], $params);
        $result = $this->app->request->post($url, $params);
        if ($result->errMsg && $result->errCode) {
            return $this->app->exception->request($result->errMsg, $result->errCode);
        }
        return $result;
    }
    
    /**
     * 上传永久图片素材
     * @param string $filename
     * @return Result
     */
    public function uploadImage($filename)
    {
        return $this->upload($filename, Media::FILE_TYPE_IMAGE);
    }
    
    /**
     * 上传永久语音素材
     * @param string $filename
     * @return Result
     */
    public function uploadVoice($filename)
    {
        return $this->upload($filename, Media::FILE_TYPE_VOICE);
    }
    
    /**
     * 上传永久视频素材
     * @param string $filename
     * @param string $title
     * @param string $introduction
     * @return Result
     */
    public function uploadVideo($filename, $title, $introduction)
    {
        return $this->upload($filename, Media::FILE_TYPE_VIDEO, [
            'description' => Helper::jsonEncode([
                'title'        => $title,
                'introduction' => $introduction
            ])
        ]);
    }
    
    /**
     * 上传永久图片素材
     * @param string $filename
     * @return Result
     */
    public function uploadThumb($filename)
    {
        return $this->upload($filename, Media::FILE_TYPE_THUMB);
    }
    
    /**
     * 上传图文消息内的图片获取URL
     * @param $filename
     * @return Result
     */
    public function uploadImg($filename)
    {
        if (!is_file($filename)) {
            return $this->app->exception->error('文件不存在: ' . $filename);
        }
        $data   = [
            'media' => new \CURLFile($filename)
        ];
        $result = $this->app->request->post(self::API_UPLOAD_IMG . $this->getAccessToken(), $data);
        if ($result->errMsg && $result->errCode) {
            return $this->app->exception->request($result->errMsg, $result->errCode);
        }
        return $result;
    }
    
}