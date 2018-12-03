<?php

namespace doyzheng\weixin\base;

/**
 * 助手类
 * Class Helper
 * @package doyzheng\weixin\core
 */
class Helper
{
    
    /**
     * 数组转换XML
     * @param      $obj
     * @param bool $withRootNode
     * @return bool|string
     */
    public static function array2xml($obj, $withRootNode = true)
    {
        if (!is_array($obj) || count($obj) <= 0) {
            return false;
        }
        $xml = '';
        foreach ($obj as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . self::array2xml($val, false) . "</" . $key . ">";
            } else if (is_numeric($val) || !preg_match("/[^A-Za-z]+/", $val)) {
                // 如果字符串是纯数字或纯字母直接显示
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        if ($withRootNode) {
            $xml = join("", array("<xml>", $xml, "</xml>"));
        }
        return $xml;
    }
    
    /**
     * XMl转换数组
     * @param string $xml
     * @return array
     */
    public static function xml2array($xml)
    {
        if (static::isXml($xml)) {
            //禁止引用外部xml实体
            $disableLibxmlEntityLoader = libxml_disable_entity_loader(true); //改为这句
            //将XML转为array
            $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            libxml_disable_entity_loader($disableLibxmlEntityLoader); //添加这句
            return $result;
        }
        return [];
    }
    
    /**
     * 获取当前的请求的url地址
     * @return string
     */
    public static function getSelfUrl()
    {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self     = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info    = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url   = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }
    
    /**
     * 判断是否为xml数据
     * @param string $xmlStr
     * @return bool
     */
    public static function isXml($xmlStr)
    {
        if ($xmlStr) {
            $xmlObj = xml_parser_create();
            if (xml_parse($xmlObj, $xmlStr, true)) {
                xml_parser_free($xmlObj);
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param mixed $value
     * @param int   $options
     * @return string
     */
    public static function jsonEncode($value, $options = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($value, $options);
    }
    
    /**
     * @param string $json
     * @param bool   $assoc
     * @return array
     */
    public static function jsonDecode($json, $assoc = true)
    {
        if (!is_string($json)) {
            return [];
        }
        if (empty($json)) {
            return [];
        }
        $json = trim($json);
        if (substr($json, 0, 1) != '{') {
            return [];
        }
        if (substr($json, -1, 1) != '}') {
            return [];
        }
        return (array)json_decode($json, $assoc);
    }
    
    /**
     * 数组合并
     * @param array $a
     * @param array $b
     * @return array|mixed
     */
    public static function arrayMerge($a, $b)
    {
        $args = func_get_args();
        $res  = array_shift($args);
        while (!empty($args)) {
            foreach (array_shift($args) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res)) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::arrayMerge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }
        
        return $res;
    }
    
    /**
     * 生成随机字符串
     * @param int $strLength
     * @return string
     */
    public static function generateRandStr($strLength = 16)
    {
        $seeds = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890';
        $min   = 0;
        $max   = strlen($seeds) - 1;
        $str   = '';
        for ($i = 0; $i < $strLength; ++$i) {
            $str .= substr($seeds, mt_rand($min, $max), 1);
        }
        return $str;
    }
    
    /**
     * 数据签名sha256算法
     * @param array  $data
     * @param string $key
     * @return string
     */
    public static function makeSignSha256($data, $key)
    {
        $data = array_filter($data);
        ksort($data);
        $params = http_build_query($data) . '&key=' . $key;
        $params = urldecode($params);
        $sign   = strtoupper(hash_hmac("sha256", $params, $key));
        return $sign;
    }
    
    /**
     * 数据签名MD5算法
     * @param $data
     * @param $key
     * @return string
     */
    public static function makeSignMd5($data, $key)
    {
        $data = array_filter($data);
        ksort($data);
        $params = http_build_query($data) . '&key=' . $key;
        $params = urldecode($params);
        $sign   = strtoupper(md5($params));
        return $sign;
    }
    
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @access public
     * @param  string  $name    字符串
     * @param  integer $type    转换类型
     * @param  bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }
        
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
    
    /**
     * 获取对象或数组的值
     * @param array  $array
     * @param string $key
     * @param null   $default
     * @return mixed|null
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }
        
        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }
        
        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }
        
        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key   = substr($key, $pos + 1);
        }
        
        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            return $array->$key;
        } elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }
        
        return $default;
    }
    
    /**
     * 获取客户端Ip
     * @return string
     */
    public static function getClientIp()
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else {
            $ip = "";
        }
        return $ip;
    }
    
    /**
     * 获取服务器Ip
     */
    public static function getServiceIp()
    {
    
    }
    
    /**
     * 友好输出
     */
    public static function dump()
    {
        static $_static = array();
        foreach (func_get_args() as $var) {
            if (empty($_static)) {
                $_static = true;
                header('content-type:text/html;charset=utf-8');
            }
            echo '<pre><hr>';
            if (is_null($var) || is_bool($var)) {
                var_dump($var);
            } else if (is_array($var)) {
                echo print_r($var, true);
            } else {
                print_r($var);
            }
            echo '</pre>';
            flush();
        }
        die;
    }
    
    /**
     * 创建目录
     * @param $path
     * @return mixed
     */
    public static function mkdir($path)
    {
        if (static::createDirectory($path)) {
            return realpath($path);
        }
        return '';
    }
    
    /**
     * @param      $path
     * @param int  $mode
     * @param bool $recursive
     * @return bool
     */
    private static function createDirectory($path, $mode = 0777, $recursive = true)
    {
        if (is_dir($path)) {
            return true;
        }
        $parentDir = dirname($path);
        // recurse if parent dir does not exist and we are not at the root of the file system.
        if ($recursive && !is_dir($parentDir) && $parentDir !== $path) {
            static::createDirectory($parentDir, $mode, true);
        }
        try {
            if (!mkdir($path, $mode)) {
                return false;
            }
            return chmod($path, $mode);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * @param $filename
     * @param $data
     * @param $flags
     * @return bool
     */
    public static function writeFile($filename, $data, $flags = 0)
    {
        Helper::mkdir(dirname($filename));
        return file_put_contents($filename, $data, $flags) != 0;
    }
    
    /**
     * @param $filename
     * @return string
     */
    public static function readFile($filename)
    {
        if (is_file($filename)) {
            return file_get_contents($filename);
        }
        return '';
    }
    
}
