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
     * 工厂方法（创建当前模块下实例）
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
    
    /**
     * 获取接口访问token
     * @return string
     */
    public function getAccessToken($isRefresh = false)
    {
        return $this->app->accessToken->getToken($isRefresh);
    }
    
}
