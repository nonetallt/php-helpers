<?php

namespace Nonetallt\Helpers\Arrays\Traits;

use Nonetallt\Helpers\Validation\Validator;

trait ConstructedFromArray
{

    public static function fromArray(array $array, string $class = null)
    {
        $mapping = self::constructorMapping();
        self::validateArrayValues($array, $mapping);

        $mapped = [];
        foreach($mapping as $key => $index) {
            $mapped[$index] = $array[$key];
        }

        $class = $class ?? self::class;
        return new $class(...$mapped);
    }

    private static function constructorMapping()
    {
        $reflection = new \ReflectionClass(self::class);
        $constructor = $reflection->getConstructor();

        $mapping = [];
        foreach($constructor->getParameters() as $parameter) {
            $mapping[$parameter->name] = $parameter->getPosition();
        }

        return $mapping;
    }

    private static function validateArrayValues(array $array, array $mapping)
    {
        $required = array_keys($mapping);
        $missing = array_keys_missing($required, $array);

        if(! empty($missing)) {
            $class = get_class(self);
            $keys = implode(', ', $missing);
            $msg = "Cannot create $class from array ,missing required keys: $keys";
            throw new \InvalidArgumentException($msg);
        }

        $validator = new Validator(self::arrayValidationRules());

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
    private static function arrayValidationRules()
    {
        return [];
    }
}
