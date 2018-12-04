<?php

namespace doyzheng\weixin\mini;

use doyzheng\weixin\base\BaseObject;

/**
 * Class Base
 * @package doyzheng\weixin\mp
 */
class Base extends BaseObject
{
    
    /**
     * 获取接口token
     * @param bool $isRefresh
     * @return string
     */
    public function getAccessToken($isRefresh = false)
    {
        return $this->app->accessToken->getToken($isRefresh);
    }
    
}