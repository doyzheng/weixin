<?php

namespace doyzheng\weixin\core;

use doyzheng\weixin\base\BaseObject;
use doyzheng\weixin\base\BaseWeixin;
use doyzheng\weixin\Weixin;

/**
 * 容器类
 * Class Container
 * @package doyzheng\weixin\core
 */
class Container
{
    
    /**
     * 缓存的实例对象
     * @var array
     */
    private $_instances = [];
    
    /**
     * 自定义类包
     * @var array
     */
    public $class = [];
    
    /**
     * @var Weixin
     */
    public $weixin;
    
    /**
     * Container constructor.
     * @param $class
     * @param $config
     */
    public function __construct($weixin, $class = [])
    {
        $this->class  = $class;
        $this->weixin = $weixin;
    }
    
    /**
     * 获取容器内的实例
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        // 是否缓存实例
        if (isset($this->_instances[$name])) {
            return $this->_instances[$name];
        }
        
        if (isset($this->class[$name])) {
            $className = $this->class[$name];
        } else {
            $className = $name;
        }
        
        if (class_exists($className)) {
            $config = array_merge($this->weixin->config, ['container' => $this]);
            $class = new $className($config);
            if ($class instanceof BaseObject) {
                $this->_instances[$name] = $class;
            }
        }
        
        if (isset($this->_instances[$name])) {
            return $this->_instances[$name];
        }
        return null;
    }
    
    /**
     * 设置容器实例
     * @param $name
     * @param $value
     * @return mixed
     */
    public function set($name, $value)
    {
        $this->_instances[$name] = $value;
        return true;
    }
    
}