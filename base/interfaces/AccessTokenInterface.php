<?php

namespace doyzheng\weixin\base\interfaces;

/**
 * Interface AccessToken
 * @package doyzheng\weixin\base
 */
interface AccessTokenInterface
{

    /**
     * 获取微信接口token
     * @param bool $isRefresh
     * @return mixed
     */
    public function getToken($isRefresh = false);

    /**
     * 设置微信token
     * @param string $token
     */
    public function setToken($accessToken);

}