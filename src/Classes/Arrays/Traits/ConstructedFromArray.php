<?php

namespace Nonetallt\Helpers\Arrays\Traits;

use Nonetallt\Helpers\Mapping\MethodParameterMapping;
use Nonetallt\Helpers\Mapping\MethodParameter;
use Nonetallt\Helpers\Generic\Exceptions\NotFoundException;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClass;
use Jawira\CaseConverter\Convert;
use Nonetallt\Helpers\Generic\Collection;

/**
 *
 * This trait allows construction of using class by calling 
 * fromArray(array $array)
 *
 * TODO better error messages, especially for nested parameters
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

        if(! $constructor->isPublic()) {
            $msg = "Can't create object with no public constructor";
            throw new MappingException($msg);
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
            $key = static::mutateConstructorArrayKey($key, $mapping);

            try {
                $methodParameter = $mapping->findParameterMapping($key);
                $result[$key] = static::mutateConstructorArrayValue($value, $methodParameter->getReflection());
            }
            catch(NotFoundException $e) {
                /* No mapping found for this parameter */
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
     * @param mixed $key String or integer key in the given array
     *
     * @return mixed $customizedKey Key that should be used
     *
     */
    protected static function mutateConstructorArrayKey($key, MethodParameterMapping $mapping) : string
    {
        $converter = new Convert($key);
        return $converter->fromSnake()->toCamel();
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
        if(! is_array($value)) {
            return $value;
        }

        /* Try converting value to class if its an array and class is required */
        if($parameter->getClass() !== null) {

            $parameterClass = $parameter->getClass()->getName();

            if(! $parameter->isVariadic()) {
                return static::convertArrayToClass($value, $parameterClass, $parameter);
            }

            $value =  array_map(function($itemValue) use ($parameterClass, $parameter){
                return self::convertArrayToClass($itemValue, $parameterClass, $parameter);
            }, $value);
        }

        return $value;
    }

    private static function convertArrayToClass(array $value, string $class, \ReflectionParameter $parameter)
    {
        $parameterClassReflection = new ReflectionClass($class);
        $traits = $parameterClassReflection->getTraits();

        /* Check that target class uses this trait */
        if(in_array(__TRAIT__, $traits)) {
            try {
                $value = $class::fromArray($value, false);
            }
            catch(MappingException $e) {
                $ref = new ReflectionClass(static::class);
                $signature = $ref->getConstructor()->getSignature();
                $position = $parameter->getPosition() + 1;
                $msg = "Argument $position of $signature could not be created from array";
                throw new MappingException($msg, 0, $e);
            }
        }

        return $value;
    }
}
