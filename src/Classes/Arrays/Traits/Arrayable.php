<?php

namespace Nonetallt\Helpers\Arrays\Traits;

/**
 * Allow converting using class to array by calling toArray() method
 *
 */
trait Arrayable
{

    /**
     *
     * Get the array representation of this object
     *
     */
    public function toArray() : array
    {
        $array = [];

        foreach($this->getArraySerializableValues() as $key => $value) {
            $array[$key] = $this->serializeArrayableValue($value);
        }

        return $array;
    }
    /**
     * Get values that should be serialized when toAray() is called
     *
     * Should be customized by user class
     *
     */
    protected function getArraySerializableValues() : array
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $props = $reflectionClass->getProperties();
        $values = [];

        foreach($props as $prop) {
            $prop->setAccessible(true);
            $values[$prop->getName()] = $prop->getValue($this);
        }

        return $values;
    }

    /**
     * Serialize a given value. Attempts to serialize objects and nested values of arrays
     *
     */
    protected function serializeArrayableValue($value)
    {
        if(is_array($value)) $value = $this->serializeArrayToArray($value); 
        elseif(is_object($value)) $value = $this->serializeObjectToArray($value);

        return $value;
    }

    /**
     * Recursively serialize all values inside the given array
     *
     */
    protected function serializeArrayToArray(array $array) : array
    {
        $result = [];
        foreach($array as $key => $value) {
            $result[$key] = $this->serializeArrayableValue($value);
        }
        return $result;
    }

    /**
     * Attempt to serialize object by calling $object->toArray();
     *
     */
    protected function serializeObjectToArray(object $object)
    {
        if(! method_exists($object, 'toArray')) return $object;

        $ref = new \ReflectionMethod($object, 'toArray');

        /* Check that toArray() can be called */
        if($ref->isPublic() && ! $ref->isStatic() && $ref->getNumberOfRequiredParameters() === 0) {
            $object = $object->toArray();
        }

        return $object;
    }
}
