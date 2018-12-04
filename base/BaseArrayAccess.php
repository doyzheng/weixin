<?php

namespace doyzheng\weixin\base;

/**
 * 数组处理基类
 * Class BaseArrayAccess
 * @package doyzheng\weixin\base
 */
abstract class BaseArrayAccess implements \ArrayAccess
{
    
    /**
     * @var array
     */
    private $_data = [];
    
    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->_data = $data;
    }
    
    /**
     * @param string $name
     * @return array|mixed
     */
    public function getData($name = null)
    {
        if ($name === null) {
            return $this->_data;
        }
        return $this->offsetGet($name);
    }
    
    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }
    
    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }
    
    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }
    
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }
    
    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if (isset($this->_data[$offset])) {
            unset($this->_data[$offset]);
        }
    }
    
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'get' . ucwords($name);
        if (method_exists($this, $getter)) {
            return $this->$getter($name);
        }
        return $this->offsetGet($name);
    }
    
}