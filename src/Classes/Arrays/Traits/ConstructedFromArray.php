<?php

namespace Nonetallt\Helpers\Arrays\Traits;

use Nonetallt\Helpers\Mapping\MethodParameterMapping;
use Nonetallt\Helpers\Mapping\MethodParameter;
use Nonetallt\Helpers\Generic\Exceptions\NotFoundException;
use Nonetallt\Helpers\Filesystem\Reflections\Psr4Reflection;

/**
 *
 * This trait allows construction of using class by calling 
 * fromArray(array $array)
 *
 */
trait ConstructedFromArray
{
    use Arrayable;

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
        $array = static::mutateConstructorArray($array, $mapping);
        $constructorParameters = $mapping->mapArray($array, $requireDefaultArgs);

        return new $class(...$constructorParameters);
    }

    /**
     *
     * Change the given array data before it is mapped to the constructor
     *
     */
    protected static function mutateConstructorArray(array $array, MethodParameterMapping $mapping) : array
    {
        $result = [];

        foreach($array as $key => $value) {
            $key = static::mutateConstructorArrayKey($key);

            try {
                $methodParameter = $mapping->findParameterMapping($key);
                $result[$key] = static::mutateConstructorArrayValue($value, $methodParameter->getReflection());
            }
            catch(NotFoundException $e) {
                /* Skip binding if there is no matching parameter in constructor */
                continue;
            }
        }

        return $result;
    }

    /**
     *
     * Change a given key before it is mapped to the constructor.
     * Convert snake case argument names to camel case.
     *
     * Can be overridden to by child class to customize functionality
     *
     * @param string $key Key in the given array
     *
     * @return string $customizedKey Key that should be used
     *
     */
    protected static function mutateConstructorArrayKey(string $key) : string
    {
        $converter = new \CaseConverter\CaseConverter();
        return $converter->convert($key)->from('snake')->to('camel');
    }

    /**
     *
     * Change a given value in the array before it is mapped to the constructor
     *
     * Automatically tries to convert nested arrays to their respective class
     * types if the classes are using this trait.
     *
     */
    protected static function mutateConstructorArrayValue($value, \ReflectionParameter $parameter)
    {
        /* Try converting value to class if its an array and class is required */
        if($parameter->getClass() !== null && is_array($value)) {

            $parameterClass = $parameter->getClass()->getName();
            $parameterClassReflection = new Psr4Reflection($parameterClass);
            $traits = $parameterClassReflection->getTraits();

            /* Check that target class uses this trait */
            if(in_array(__TRAIT__, $traits)) {
                $value = $parameterClass::fromArray($value);
            }
        }

        return $value;
    }
}
