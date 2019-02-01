<?php

namespace Nonetallt\Helpers\Generic;

use Nonetallt\Helpers\Arrays\TypedArray;

class Collection implements \Iterator, \ArrayAccess
{
    protected $items;
    private $type;

    public function __construct(array $items = [], string $type = null)
    {
        $this->position = 0;
        $this->type = $type;
        $this->setItems($items);
    }

    public function setItems(array $items)
    {
        if(! is_null($this->type)) {
            $items = TypedArray::create($this->type, $items);
        }

        $this->items = $items;
    }

    public function toArray()
    {
        return $this->items;
    }

    // ArrayAccess methods
    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset) 
    {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset) 
    {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset) 
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    // Iterator methods
    public function rewind() 
    {
        $this->position = 0;
    }

    public function current() 
    {
        return $this->items[$this->position];
    }

    public function key() 
    {
        return $this->position;
    }

    public function next() 
    {
        ++$this->position;
    }

    public function valid() 
    {
        return isset($this->items[$this->position]);
    }
}
