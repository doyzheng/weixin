<?php

namespace doyzheng\weixin\base;

/**
 * Class BaseModule
 * @package doyzheng\weixin\base
 */
abstract class BaseModule extends BaseWeixin
{
    
    /**
     * 获取子类实例
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($value = parent::__get($name)) {
            return $value;
        }
        
        // 当前命令空间下查找
        $className = static::getNamespace() . '\\' . ucwords($name);
        
        if ($class = $this->container->get($className)) {
            return $class;
        }
        
        $className = static::getNamespace() . '\\' . $name . '\\' . 'Module';
        if ($class = $this->container->get($className)) {
            return $class;
        }
        
        $className = static::getNamespace() . '\\' . $name . '\\' . ucwords($name);
        if ($class = $this->container->get($className)) {
            return $class;
        }
        
        return $this->exception->unknownClass($className);
    }
    
}