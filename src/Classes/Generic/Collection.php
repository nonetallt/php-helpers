<?php

namespace Nonetallt\Helpers\Generic;

use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Describe\DescribeObject;

/**
 * Array style storage with inbuilt array functions.
 *
 */
class Collection implements \IteratorAggregate, \ArrayAccess, \Countable
{
    CONST COLLECTION_TYPE = null;

    protected $items;

    public function __construct(array $items = [])
    {
        $this->items = [];  
        $this->pushAll($items);
    }

    public function validateItem($item)
    {
        if(static::COLLECTION_TYPE === null) {
            return;
        }

        $type = gettype($item);

        if($type === 'object') {
            if(is_a($item, static::COLLECTION_TYPE)) return;
        }
        else {
            if($type === static::COLLECTION_TYPE) return;
        }
            
        $given = (new DescribeObject($item))->describeType();
        $type = static::COLLECTION_TYPE;
        $msg = "Pushed item must be of type $type, $given given";
        throw new \InvalidArgumentException($msg);
    }

    public function push($item)
    {
        $this[] = $item;
    }

    public function pushAll(iterable $items)
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
            if($returned === null) continue;
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
        $actual = $items::COLLECTION_TYPE ?? 'null';
        $expected = static::COLLECTION_TYPE ?? 'null';

        if($actual !== $expected && ! is_a($actual, $expected, true)) {
            $msg = "Can't merge collections of type $expected and $actual";
            throw new \InvalidArgumentException($msg);
        }

        $array = array_merge($this->items, $items->toArray());
        $class = get_class($this);

        return new $class($array);
    }


    public function filterByClass(string $filterClass)
    {
        $collectionClass = get_class($this);
        $collection = new $collectionClass([]);

        foreach($this->items as $item) {
            if(is_a($item, $filterClass)) $collection->push($item);
        }

        return $collection;
    }

    public function getItemTypes() : array
    {
        return $this->map(function($item) {
            $desc = new DescribeObject($item);
            return $desc->describeType();
        });
    }

    // ArrayAccess methods
    public function offsetSet($offset, $value) 
    {
        $this->validateItem($value);

        if ($offset === null) {
            $this->items[] = $value;
        } 
        else {
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

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
