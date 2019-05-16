<?php

namespace Nonetallt\Helpers\Arrays\Traits;

use Nonetallt\Helpers\Validation\Validator;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;

trait ConstructedFromArray
{
    public static function fromArray(array $array, string $class = null)
    {
        $class = $class ?? self::class;
        $mapping = self::constructorMapping($class);

        /* if class defines mapping, transform given array keys according to mapping */
        if(method_exists($class, 'arrayToConstructorMapping')) {
            $conversionMapping = $class::arrayToConstructorMapping();
            foreach($conversionMapping as $from => $to) {

                /* Do not try to map if source value is not found from array */
                if(! isset($array[$from])) continue;

                $value = $array[$from];
                unset($array[$from]);
                $array[$to] = $value;
            }
        }

        self::validateArrayValues($array, $mapping, $class);

        $mapped = [];
        foreach($mapping as $key => $index) {
            if(! isset($array[$key])) continue;
            $mapped[$index] = $array[$key];
        }

        return new $class(...$mapped);
    }

    private static function requiredKeys($class)
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        $requiredKeys = [];

        foreach($constructor->getParameters() as $parameter) {
            if(! $parameter->isDefaultValueAvailable()) $requiredKeys[] = $parameter->name;
        }

        return $requiredKeys;
    }

    private static function constructorMapping(string $class)
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        $mapping = [];

        foreach($constructor->getParameters() as $parameter) {
            $mapping[$parameter->name] = $parameter->getPosition();
        }

        return $mapping;
    }

    /**
     * @throws Nonetallt\Helpers\Validation\Exceptions\ValidationException
     */
    private static function validateRequiredArrayKeys(array $array, string $class)
    {
        $missing = array_keys_missing(self::requiredKeys($class), $array);
        if(empty($missing)) return;

        $class = self::class;
        $keys = implode(', ', $missing);
        $msg = "Cannot create $class from array, missing required keys: $keys";
        throw new ValidationException($msg);
    }

    /**
     * @throws Nonetallt\Helpers\Validation\Exceptions\ValidationException
     */
    private static function validateArrayValues(array $array, array $mapping, string $class)
    {
        self::validateRequiredArrayKeys($array, $class);
        $validator = new Validator($class::arrayValidationRules());

        if($validator->passes($array)) return;
        throw new ValidationException($validator->getErrors());
    }

    /**
     * This method is ment to be overridden by implementing class
     *
     * @return string $rules
     */
    protected static function arrayValidationRules()
    {
        return [];
    }
}
