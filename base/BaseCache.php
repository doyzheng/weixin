<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\base\interfaces\CacheInterface;

/**
 * 缓存对象基类
 * Class BaseCache
 * @package doyzheng\weixin\base
 */
abstract class BaseCache extends BaseObject implements CacheInterface
{
    
    /**
     * @var CacheInterface 缓存对象
     */
    public $drive;
    
    /**
     * @param $key
     * @return mixed|string
     */
    public function buildKey($key)
    {
        return md5($key);
    }
    
    /**
     * 设置缓存
     * @param string   $key
     * @param mixed    $value
     * @param null|int $duration
     * @return mixed
     */
    public function set($key, $value, $duration = null)
    {
        return $this->drive->set($key, $value, $duration);
    }
    
    /**
     * 获取缓存
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->drive->get($key);
    }
    
    /**
     * 删除缓存
     * @param string $key
     * @return mixed
     */
    public function delete($key)
    {
        return $this->drive->delete($key);
    }
    
    /**
     * 缓存是否存在
     * @param string $key
     * @return mixed
     */
    public function exists($key)
    {
        return $this->drive->exists($key);
    }
    
    /**
     * @param mixed $offset
     * @return bool|mixed
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }
    
    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * @param mixed $offset
     * @param mixed $value
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }
    
    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetUnset($offset)
    {
        return $this->set($offset, null);
    }
    
}