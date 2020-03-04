<?php

namespace Nonetallt\Helpers\Validation\Parameters;

class ParameterContainer implements \ArrayAccess ,\IteratorAggregate
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function isStrict()
    {
        return $this->strict;
    }

    public function getData()
    {
        return $this->data;
    }

    public function toArray()
    {
        return $this->data;
    }
     
    /**
     * implement ArrayAccess
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * implement ArrayAccess
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * implement ArrayAccess
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * implement ArrayAccess
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * implement IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}
