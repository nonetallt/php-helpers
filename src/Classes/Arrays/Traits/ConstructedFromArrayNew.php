<?php

namespace Nonetallt\Helpers\Arrays\Traits;

use Nonetallt\Helpers\Mapping\MethodParameterMapping;

trait ConstructedFromArrayNew
{
    /**
     * Construct trait using class from data in the given array.
     *
     * @param array $array
     *
     * @param bool $requireDefaultArgs If set to true exception will be thrown
     * when array data is missing a constructor argument with default value
     *
     * @return $traitUsingClass New class instance
     *
     */
    public static function fromArray(array $array, bool $requireDefaultArgs = false)
    {
        $class = static::class;
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        
        /* If there is no constructor, create class without args */
        if($constructor === null) {
            return new $class();
        }

        $mapping = new MethodParameterMapping($constructor);
        $constructorParameters = $mapping->mapArray($array, $requireDefaultArgs);

        return new $class(...$constructorParameters);
    }
}
