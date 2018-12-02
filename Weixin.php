<?php

namespace doyzheng\weixin;

use doyzheng\weixin\base\Container;
use doyzheng\weixin\base\Helper;

/**
 * Class Weixin
 * @package doyzheng\weixin
 * @property base\AccessToken $accessToken
 * @property base\Cache       $cache
 * @property base\Request     $request
 * @property base\Exception   $exception
 * @property base\Log         $log
 * @property mp\Module        $mp
 * @property mini\Module      $mini
 * @property open\Module      $open
 * @property parking\Module   $parking
 */
class Weixin
{
    
    /**
     * 储存对象的容器
     * @var Container
     */
    public $container;
    
    /**
     * 项目运行目录
     * @var string
     */
    public $runtimePath;
    
    /**
     * 是否开启调试模式
     * @var bool
     */
    public $appDebug = false;
    
    /**
     * Weixin constructor.
     * @param $config
     */
    public function __construct($config = [])
    {
        $mapClass        = [
            // 核心类
            'cache'       => 'doyzheng\weixin\base\Cache',
            'log'         => 'doyzheng\weixin\base\Log',
            'request'     => 'doyzheng\weixin\base\Request',
            'session'     => 'doyzheng\weixin\base\Session',
            'accessToken' => 'doyzheng\weixin\base\AccessToken',
            'exception'   => 'doyzheng\weixin\base\Exception',
            // 模块类
            'mp'          => 'doyzheng\weixin\mp\Module',
            'mini'        => 'doyzheng\weixin\mini\Module',
            'open'        => 'doyzheng\weixin\open\Module',
            'parking'     => 'doyzheng\weixin\parking\Module',
        ];
        $this->container = new Container($this, $mapClass, $config);
        // 设置运行目录目录
        if (isset($config['runtimePath']) && is_dir($config['runtimePath'])) {
            $this->runtimePath = realpath($config['runtimePath']);
        } else {
            $this->runtimePath = Helper::mkdir('../runtime/weixin');
        }
    }
    
    /**
     * 获取项目版本号
     * @return string 版本号
     */
    public static function getVersion()
    {
        return '1.2';
    }
    
    /**
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        return $this->container->get($name);
    }
    
    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->container->set($name, $value);
    }
    
    /**
     * @param $name
     * @return bool|mixed|null
     */
    public function __get($name)
    {
        return $this->get($name);
    }
    
    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
    
    /**
     * 配置对象
     * @param $object
     * @param $config
     */
    public static function configure($object, $config)
    {
        if (is_array($config)) {
            foreach ($config as $name => $value) {
                if (property_exists($object, $name)) {
                    $object->{$name} = $value;
                }
            }
        }
    }
    
    /**
     * 判断是否为微信客户端
     */
    public static function isWeChat()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
    
}