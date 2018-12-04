<?php

namespace doyzheng\weixin\base;

/**
 * 模块基类
 * Class BaseModule
 * @package doyzheng\weixin\base
 */
abstract class BaseModule extends BaseObject
{
    
    /**
     * 工厂方法
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        // 当前命令空间下查找
        $className = static::getNamespace() . '\\' . ucwords($name);
        if ($class = $this->app->get($className)) {
            return $class;
        }
        
        $className = static::getNamespace() . '\\' . $name . '\\' . 'Module';
        if ($class = $this->app->get($className)) {
            return $class;
        }
        
        $className = static::getNamespace() . '\\' . $name . '\\' . ucwords($name);
        if ($class = $this->app->get($className)) {
            return $class;
        }
        
        return parent::__get($name);
    }
    
}
