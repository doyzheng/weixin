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
    public $disable = true;
    
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
