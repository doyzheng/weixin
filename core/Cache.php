<?php

namespace doyzheng\weixin\core;

use doyzheng\weixin\base\BaseCache;

/**
 * 文件方式数据缓存类
 * Class Cache
 * @package doyzheng\weixin\core
 */
class Cache extends BaseCache
{
    
    /**
     * @var string 缓存目录
     */
    public $savePath;
    
    /**
     * 初始化
     */
    public function init()
    {
        // 默认缓存目录
        if (!$this->savePath) {
            $this->savePath = Helper::mkdir($this->container->weixin->runtimePath . '/cache');
        }
    }
    
    /**
     * @param string $key
     * @param string $value
     * @param null   $duration
     * @return mixed
     */
    public function set($key, $value, $duration = null)
    {
        $data     = [
            'key'         => $key,
            'value'       => $value,
            'create_time' => time(),
            'duration'    => $duration,
        ];
        $filename = $this->getFilenameByKey($key);
        return Helper::writeFile($filename, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->exists($key)) {
            return null;
        }
        $filename = $this->getFilenameByKey($key);
        $content  = Helper::readFile($filename);
        $data = json_decode($content, true);
        if (!$data) {
            $this->delete($key);
            return null;
        }
        if ($data['duration']) {
            if (time() - $data['create_time'] > $data['duration']) {
                $this->delete($key);
                return null;
            }
        }
        return $data['value'];
    }
    
    /**
     * 删除缓存
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $filename = $this->getFilenameByKey($key);
        if (is_file($filename)) {
            return unlink($filename);
        }
        return false;
    }
    
    /**
     * 缓存是否存在
     * @param string $key
     * @return bool|mixed
     */
    public function exists($key)
    {
        return is_file($this->getFilenameByKey($key));
    }
    
    /**
     * @param $key
     * @return string
     */
    private function getKey($key)
    {
        return md5($key);
    }
    
    /**
     * 获取文件名
     * @param string $key
     * @return string
     */
    private function getFilenameByKey($key)
    {
        return $this->savePath . '/' . $this->getKey($key) . '.json';
    }
    
}
