<?php

namespace doyzheng\weixin\base\cache;

use doyzheng\weixin\base\BaseCache;
use doyzheng\weixin\base\Helper;

/**
 * 文件缓存类
 * Class File
 * @package doyzheng\weixin\base\cache
 */
class File extends BaseCache
{
    
    /**
     * @var string
     */
    public $savePath;
    
    /**
     * 设置缓存
     * @param string   $key
     * @param mixed    $value
     * @param null|int $duration
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
     * 获取缓存
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
        $data     = json_decode($content, true);
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
     * @return mixed
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
     * @return mixed
     */
    public function exists($key)
    {
        return is_file($this->getFilenameByKey($key));
    }
    
    /**
     * 获取文件名
     * @param string $key
     * @return string
     */
    private function getFilenameByKey($key)
    {
        return $this->savePath . '/' . $this->buildKey($key) . '.json';
    }
    
}