<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\core\AccessToken;
use doyzheng\weixin\core\Exception;
use doyzheng\weixin\core\Request;
use doyzheng\weixin\core\Cache;
use doyzheng\weixin\core\Log;

/**
 * 微信类库基础类(所有类必须继承)
 * Class BaseWx
 * @package doyzheng\weixin\core
 * @property Exception   $exception
 * @property bool        $appDebug
 * @property AccessToken $accessToken
 * @property Cache       $cache
 * @property Request     $request
 * @property Log         $log
 *
 */
abstract class BaseWeixin extends BaseObject
{

}