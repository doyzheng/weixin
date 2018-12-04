<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\Weixin;

/**
 * 类库基类,所有扩展类都应该继承
 * Class BaseObject
 * @package doyzheng\weixin\base
 */
abstract class BaseObject
{
    
    /**
     * @var Weixin
     */
    public $app;
    
    /**
     * BaseObject constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        Weixin::configure($this, $config);
        $this->init();
    }
    
    /**
     * 初始化
     */
    protected function init()
    {
    }
    
    /**
     * 获取对象的属性值
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'get' . ucwords($name);
        if (method_exists($this, $getter)) {
            return $this->$getter($name);
        }
        return $this->app->exception->unknownProperty('Getting unknown property:' . get_class($this) . '::' . $name);
    }
    
    /**
     * 设置对象的属性值
     * @param string $name
     * @param mixed  $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $setter = 'set' . ucwords($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($name, $value);
        }
        return $this->app->exception->unknownProperty('Setting unknown property:' . get_class($this) . '::' . $name);
    }
    
    /**
     * 判断对象属性是否存在
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter($name) !== null;
        }
        
        return false;
    }
    
    
    /**
     * @param $name
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($name, null);
        }
        $this->app->exception->unknownProperty('UnSetting unknown property:' . get_class($this) . '::' . $name);
    }
    
    /**
     * 调用类方法
     * @param string $name
     * @param        $params
     */
    public function __call($name, $params)
    {
        $this->app->exception->unknownMethod('Calling unknown method: ' . get_class($this) . "::$name()");
    }
    
    /**
     * 获取对象名
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
    
}