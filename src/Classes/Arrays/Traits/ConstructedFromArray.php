<?php

namespace Nonetallt\Helpers\Arrays\Traits;

use Nonetallt\Helpers\Validation\Validator;

trait ConstructedFromArray
{

    public static function fromArray(array $array, string $class = null)
    {
        $class = $class ?? self::class;
        $mapping = self::constructorMapping($class);
        self::validateArrayValues($array, $mapping, $class);

        $mapped = [];
        foreach($mapping as $key => $index) {
            $mapped[$index] = $array[$key];
        }

        return new $class(...$mapped);
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

    private static function validateArrayValues(array $array, array $mapping, string $class)
    {
        $required = array_keys($mapping);
        $missing = array_keys_missing($required, $array);

        if(! empty($missing)) {
            $class = self::class;
            $keys = implode(', ', $missing);
            $msg = "Cannot create $class from array, missing required keys: $keys";
            throw new \InvalidArgumentException($msg);
        }

        $validator = new Validator($class::arrayValidationRules());

        if(! $validator->fails($array)) return;

        $msg = "";
        $errors = $validator->getErrors();
        foreach($errors as $key => $messages) {

            $paramErrors = array_map(function($message) {
                return "- $message";
            }, $messages);
            $paramErrors = implode(PHP_EOL, $paramErrors);

            $msg .= PHP_EOL . "Validation for $key failed:" . PHP_EOL . $paramErrors;
        }

        throw new \InvalidArgumentException($msg);
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
