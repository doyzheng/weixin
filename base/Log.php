<?php

namespace doyzheng\weixin\base;

use doyzheng\weixin\base\interfaces\LogInterface;

/**
 * 日志处理类
 * Class Log
 * @package doyzheng\weixin\base
 */
class Log extends BaseObject implements LogInterface
{
    
    /**
     * @var string 日志保存目录
     */
    public $savePath;
    
    /**
     * @var bool 是否禁用日志
     */
    public $disable = false;
    
    /**
     * 初始化
     */
    public function init()
    {
        if (!$this->savePath) {
            $this->savePath = Helper::mkdir($this->app->runtimePath . '/logs');
        }
    }
    
    /**
     * 添加日志数
     * @param string       $type
     * @param array|string $data
     * @return bool
     */
    public function add($type, $data)
    {
        if (!$this->disable) {
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
     * 请求日志
     * @param array $data
     * @return bool
     */
    public function request($data)
    {
        return $this->add('request', $data);
    }
    
    /**
     * 异常日志
     * @param \Exception $exception
     * @return bool
     */
    public function error($exception)
    {
        return $this->add('error', (string)$exception);
    }
    
    /**
     * 写回调通知日志
     * @param string $name
     * @return bool|mixed
     */
    public function notify($message)
    {
        return $this->add('notify', [
            'GET'     => $_GET,
            'POST'    => $_POST,
            'REQUEST' => $_REQUEST,
            'COOKIE'  => $_COOKIE,
            'FILES'   => $_FILES,
            'SERVER'  => $_SERVER,
            'RAW'     => file_get_contents('php://input'),
            'REPLY'   => $message,
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
