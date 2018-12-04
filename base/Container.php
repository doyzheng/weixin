<?php

namespace doyzheng\weixin\base;

/**
 * 容器类
 * Class Container
 * @package doyzheng\weixin\base
 */
class Container
{
    
    /**
     * 缓存的实例对象
     * @var array
     */
    private $_instances = [];
    
    /**
     * 类名映射数组
     * @var array
     */
    public $classMap = [];
    
    /**
     * @var array
     */
    public $config = [];
    
    /**
     * @var object
     */
    public $weixin;
    
    /**
     * Container constructor.
     * @param object $weixin
     * @param array  $classMap
     * @param array  $config 全局配置
     */
    public function __construct($weixin, $classMap = [], $config = [])
    {
        $this->weixin   = $weixin;
        $this->classMap = $classMap;
        $this->config   = $config;
    }
    
    /**
     * 获取容器内的实例
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        // 检查这个对象是否已经创建
        if (isset($this->_instances[$name])) {
            return $this->_instances[$name];
        }
        // 检查对象名是否在类名映射数组中
        if (isset($this->classMap[$name])) {
            $className = $this->classMap[$name];
        } else {
            $className = $name;
        }
        // 如果类存在，去实例化
        if (class_exists($className)) {
            $config = array_merge($this->config, ['app' => $this->weixin]);
            // 实例这个类
            $class = new $className($config);
            // 这个类必须继承BaseObject
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
     * 设置容器中的对象
     * @param string $name
     * @param mixed  $object
     * @return bool
     */
    public function set($name, $object)
    {
        if ($object instanceof BaseObject) {
            $this->_instances[$name] = $object;
            return true;
        }
        return false;
    }
    
    /**
     * 扩展一个新实例
     * @param string $name
     * @param mixed  $class
     * @return bool
     */
    public function extend($name, $class)
    {
        $this->classMap[$name] = $class;
        return true;
    }
    
    /**
     * 检查实例是否存在
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if (isset($this->classMap[$name])) {
            return true;
        }
        return isset($this->_instances[$name]);
    }
    
}