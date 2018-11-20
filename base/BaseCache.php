<?php

namespace doyzheng\weixin\base;

/**
 * 缓存基类
 * Class BaseCache
 * @package doyzheng\weixin\base
 */
abstract class BaseCache extends BaseWeixin
{
    
    /**
     * 设置缓存
     * @param string   $key
     * @param mixed    $value
     * @param null|int $duration
     * @return mixed
     */
    abstract public function set($key, $value, $duration = null);
    
    /**
     * 获取缓存
     * @param string $key
     * @return mixed
     */
    abstract public function get($key);
    
    /**
     * 删除缓存
     * @param string $key
     * @return mixed
     */
    abstract public function delete($key);
    
    /**
     * 缓存是否存在
     * @param string $key
     * @return mixed
     */
    abstract public function exists($key);
    
}
