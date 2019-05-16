<?php

namespace Nonetallt\Helpers\Validation\Parameters;

class SimpleContainer implements \ArrayAccess
{
    private $name;
    private $data;
    private $strict;

    public function __construct(string $name, array $data, bool $strict = false)
    {
        $this->name = $name;
        $this->data = $data;
        $this->strict = $strict;
    }

    public function __get(string $key)
    {
        if(! isset($this->data[$key])) {
            if($this->strict) throw new \Exception("Value $key does not exist in container $this->name");
            return null;
        }

        return $this->data[$key];
    }

    public function getName()
    {
        return $this->name;
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
        return [
            'name' => $this->name,
            'data' => $this->data,
            'is_strict' => $this->strict
        ];
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
}
