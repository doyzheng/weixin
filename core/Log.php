<?php

namespace doyzheng\weixin\core;

use doyzheng\weixin\base\BaseLog;

/**
 * Class Log
 * @package doyzheng\weixin\core
 */
class Log extends BaseLog
{
    
    /**
     * 初始化
     */
    public function init()
    {
        if (!$this->savePath) {
            $this->savePath = Helper::mkdir($this->container->weixin->runtimePath . '/logs');
        }
    }
    
    /**
     * 添加日志数
     * @param string $type
     * @param mixed  $data
     * @return bool
     */
    public function add($type, $data)
    {
        if ($this->disable != false) {
            $filename = $this->generateFilename($type);
            $str      = is_file($filename) ? '' : "<?php \n";
            $str      .= "/*-------------------------------" . date('Y-m-d H:i:s') . "-------------------------------*/\n";
            $str      .= var_export($data, true) . ";\n";
            $str      .= "/*---------------------------------------end---------------------------------------*/\n\n";
            return Helper::writeFile($filename, $str, FILE_APPEND) != 0;
        }
        return false;
    }
    
    /**
     * 请求异常日志
     * @param array $data
     * @return bool|mixed
     */
    public function weixinError($data)
    {
        return $this->add('weixin_error', $data);
    }
    
    /**
     * 异常日志
     * @param \Exception $exception
     * @return bool|mixed
     */
    public function error($exception)
    {
        return $this->add('error', [
            'Message'   => $exception->getMessage(),
            'Line'      => $exception->getLine(),
            'Code'      => $exception->getCode(),
            'File'      => $exception->getFile(),
            'Exception' => $exception,
        ]);
    }
    
    /**
     * 请求日志
     * @param array $data
     * @return bool|mixed
     */
    public function request($data)
    {
        return $this->add('request', $data);
    }
    
    /**
     * 写访问日志
     * @param string $name
     * @return bool|mixed
     */
    public function access($name = '')
    {
        return $this->add($name ? $name : 'access', [
            'GET'     => $_GET,
            'POST'    => $_POST,
            'REQUEST' => $_REQUEST,
            'SERVER'  => $_SERVER,
            'COOKIE'  => $_COOKIE,
            'FILES'   => $_FILES,
            'RAW'     => file_get_contents('php://input'),
        ]);
    }
    
    /**
     * 生成日志文件名
     * @param string $type
     * @return mixed
     */
    private function generateFilename($type)
    {
        return $this->savePath . '/' . $type . '_' . date('Ymd') . '.php';
    }
    
}
