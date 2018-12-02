<?php

namespace doyzheng\weixin\base\interfaces;

/**
 * Interface CacheInterface
 * @package doyzheng\weixin\base\interfaces
 */
interface CacheInterface extends \ArrayAccess
{
    
    /**
     * 生产一个新的缓存key
     * @return mixed
     */
    public function buildKey($key);
    
    /**
     * 设置缓存
     * @param string   $key
     * @param mixed    $value
     * @param null|int $duration
     * @return mixed
     */
    public function set($key, $value, $duration = null);
    
    /**
     * 获取缓存
     * @param string $key
     * @return mixed
     */
    public function get($key);
    
    /**
     * 删除缓存
     * @param string $key
     * @return mixed
     */
    public function delete($key);
    
    /**
     * 缓存是否存在
     * @param string $key
     * @return mixed
     */
    public function exists($key);
    
    /**
     * 是否存在偏移量
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset);
    
    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset);
    
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value);
    
    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset);
    
}