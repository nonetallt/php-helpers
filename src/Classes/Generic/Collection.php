<?php

namespace Nonetallt\Helpers\Generic;

use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Describe\DescribeObject;

/**
 * Array style storage with inbuilt array functions.
 * Does not support string keys, if you need strings as keys use 
 * Nonetallt\Helpers\Generic\Container instead.
 */
class Collection implements \Iterator, \ArrayAccess
{
    protected $items;
    private $type;
    private $position;

    public function __construct(array $items = [], ?string $type = null)
    {
        $this->position = 0;
        $this->type = $type;
        $this->setItems($items);
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        if($this->type !== null && ! $this->isEmpty()) {
            $msg = "Can't change type to $type from current $this->type when there are already items";
            throw new \Exception($msg);
        }

        $this->type = $type;
    }

    public function setItems(array $items)
    {
        if(! is_null($this->type)) {
            $items = TypedArray::create($this->type, $items);
        }

        $this->items = $items;
    }

    public function push($item)
    {
        if(! is_null($this->type) && ! is_a($item, $this->type)) {
            $given = (new DescribeObject($item))->describeType();
            $msg = "Pushed item must be of type $this->type, $given given";
            throw new \InvalidArgumentException($msg);
        }       

        $this->items[] = $item;
    }

    public function count()
    {
        return count($this->items);
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function first()
    {
        return $this->items[0] ?? null;
    }

    public function toArray()
    {
        return $this->items;
    }

    public function map(callable $cb)
    {
        $result = [];
        foreach($this->items as $index => $item) {
            $returned = $cb($item, $index);
            if(is_null($returned)) continue;
            $result[] = $returned;
        }

        return $result;
    }

    public function filter(callable $cb)
    {
        $result = [];
        foreach($this->items as $index => $item) {
            if($cb($item, $index)) $result[] = $item;
        }

        return $result;
    }

    public function merge(Collection $items)
    {
        $expected = $this->type;
        $actual = $items->getType();

        if($expected !== $actual) {
            throw new \InvalidArgumentException("Can't merge collections of type $expected and $actual");
        }

        $array = array_merge($this->items, $items->toArray());
        $class = get_class($this);

        return new $class($array);
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
