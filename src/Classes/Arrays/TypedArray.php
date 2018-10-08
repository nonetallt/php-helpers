<?php

namespace Nonetallt\Helpers\Arrays;

class TypedArray
{
    
    public static function create($expected, array $values)
    {
        foreach($values as $value) {

            $given = self::typeof($value);

            if($given !== $expected && ! is_subclass_of($value, $expected)) {
                $msg = "Typed array must be constructed from $expected values, $given given";
                throw new \InvalidArgumentException($msg);
            } 
        }

        return $values;
    }

    private static function typeof($value)
    {
        $type = gettype($value);
        if(is_object($value)) $type = get_class($value);

        return $type;
    }
}
