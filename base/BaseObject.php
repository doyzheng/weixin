<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\core\Container;
use doyzheng\weixin\Weixin;

/**
 * 对象基础类
 * Class BaseObject
 * @package doyzheng\weixin\core
 */
abstract class BaseObject
{
    
    /**
     * @var Container 容器
     */
    public $container;
    
    /**
     * BaseObject constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->configure($config);
        $this->init();
    }
    
    /**
     * 初始化方法
     */
    protected function init()
    {
    }
    
    /**
     * 获取类名
     * @return string
     */
    public static function getClassName()
    {
        return get_called_class();
    }
    
    /**
     * 获取类的命名空间
     * @return string
     */
    public static function getNamespace()
    {
        $className = get_called_class();
        $arr       = explode('\\', $className);
        array_pop($arr);
        return join('\\', $arr);
    }
    
    /**
     * 配置对象
     * @param $config
     */
    public function configure($config)
    {
        if (is_array($config)) {
            foreach ($config as $name => $value) {
                if (property_exists($this, $name)) {
                    $this->{$name} = $value;
                }
            }
        }
    }
    
    /**
     * 获取属性
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], []);
        }
        if ($value = $this->container->get($name)) {
            return $value;
        }
        return null;
    }
    
    /**
     * 设置属性
     * @param string $name
     * @param mixed  $value
     * @return bool|mixed
     */
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], [$value]);
            return true;
        }
        if ($value = $this->container->set($name, $value)) {
            return $value;
        }
        return false;
    }
    
}
