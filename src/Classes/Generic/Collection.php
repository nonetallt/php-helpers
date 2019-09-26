<?php

namespace Nonetallt\Helpers\Generic;

use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Describe\DescribeObject;

/**
 * Array style storage with inbuilt array functions.
 * Does not support string keys, if you need strings as keys use 
 * Nonetallt\Helpers\Generic\Container instead.
 *
 * TODO Iterator should work with string keys
 *
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

    // Getters and setters
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

    // Functionality methods
    public function push($item)
    {
        if(! is_null($this->type) && ! is_a($item, $this->type)) {
            $given = (new DescribeObject($item))->describeType();
            $msg = "Pushed item must be of type $this->type, $given given";
            throw new \InvalidArgumentException($msg);
        }       

        $this->items[] = $item;
    }

    public function pushAll(\Iterator $items)
    {
        foreach($items as $item) {
            $this->push($item);
        }
    }

    public function hasItem($item, bool $strict = true) : bool
    {
        return in_array($item, $this->items, $strict);
    }

    /**
     * Checks if this collection has at least one exception of specified class
     *
     * @param string $itemClass Full name of the class to look for
     * @param bool $allowSubclass Wether subclasses of $itemClass should
     * be accepted as a match
     *
     * @return bool $hasExceptionOfClass Wether this collection has the
     * specified class
     *
     */
    public function hasItemOfClass(string $itemClass, bool $allowSubclass = true) : bool
    {
        foreach($this->items as $item) {
            if($allowSubclass && is_subclass_of($item, $itemClass)) return true;
            if(get_class($item) === $itemClass) return true;
        }

        return false;
    }

    public function count() : int
    {
        return count($this->items);
    }

    public function isEmpty() : bool
    {
        return empty($this->items);
    }

    public function first()
    {
        $key = array_keys($this->items)[0];
        return $this->items[$key] ?? null;
    }

    public function toArray() : array
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
        $actual = $items->getType();
        $expected = $this->type;

        if($actual !== null && $expected !== null && ! is_a($actual, $expected, true)) {
            $msg = "Can't merge collections of type $expected and $actual";
            throw new \InvalidArgumentException($msg);
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
