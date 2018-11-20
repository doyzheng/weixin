<?php

namespace doyzheng\weixin;

use doyzheng\weixin\base\BaseObject;
use doyzheng\weixin\core\Container;
use doyzheng\weixin\core\Helper;

/**
 * Class Weixin
 * @package doyzheng\weixin
 * // 核心类
 * @property core\AccessToken $accessToken
 * @property core\Cache       $cache
 * @property core\Log         $log
 * @property core\Request     $request
 * @property core\Exception   $exception
 * // 项目模块
 * @property mp\Module        $mp
 * @property open\Module      $open
 * @property mini\Module      $mini
 * @property parking\Module   $parking
 */
class Weixin extends BaseObject
{
    
    /**
     * @var string 版本号
     */
    private static $version = '18.11.20';
    
    /**
     * @var bool
     */
    public $appDebug = false;
    
    /**
     * @var array
     */
    public $config = [];
    
    /**
     * @var string
     */
    public $runtimePath;

    /**
     * Weixin constructor.
     * @param array $config
     * @param array $coreClass
     */
    public function __construct(array $config = [], $coreClass = [])
    {
        parent::__construct($config);
        $class           = Helper::arrayMerge([
            // 核心类
            'cache'       => 'doyzheng\weixin\core\Cache',
            'log'         => 'doyzheng\weixin\core\Log',
            'request'     => 'doyzheng\weixin\core\Request',
            'accessToken' => 'doyzheng\weixin\core\AccessToken',
            'exception'   => 'doyzheng\weixin\core\Exception',
            // 模块类
            'mp'          => 'doyzheng\weixin\mp\Module',
            'mini'        => 'doyzheng\weixin\mini\Module',
            'open'        => 'doyzheng\weixin\open\Module',
            'parking'     => 'doyzheng\weixin\parking\Module',
        ], $coreClass);
        $this->config    = $config;
        $this->container = new Container($this, $class);
    }
    
    /**
     * 初始化
     */
    public function init()
    {
        if (!$this->runtimePath) {
            $this->runtimePath = Helper::mkdir(__DIR__ . '/../../../runtime/weixin');
        }
    }

    /**
     * 获取版本号
     * @return string
     */
    public function getVersion()
    {
        return static::$version;
    }
    
}