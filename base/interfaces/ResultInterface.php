<?php

namespace doyzheng\weixin\base\interfaces;

/**
 * Interface ResultInterface
 * @package doyzheng\weixin\base\interfaces
 */
interface ResultInterface extends \ArrayAccess
{
    
    /**
     * 获取解析后的数据
     * @param null $name
     * @return array|string|null
     */
    public function data($name = null);
    
    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset);
    
    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset);
    
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value);
    
    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset);
    
}