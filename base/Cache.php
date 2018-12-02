<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\base\cache\File;

/**
 * 缓存对象（默认使用内部文件缓存类）
 * Class Cache
 * @package doyzheng\weixin\base
 */
class Cache extends BaseCache
{
    
    /**
     * @var string
     */
    public $type = 'file';
    
    /**
     * 初始化
     */
    public function init()
    {
        if ($this->type) {
            $this->drive = new File(['savePath' => $this->app->runtimePath . '/cache']);
        }
    }
    
}
