<?php

namespace Nonetallt\Helpers\Arrays;

use Illuminate\Support\Facades\Validator;

trait ConstructedFromArray
{

    public static function fromArray(array $array)
    {
        $mapping = self::constructorMapping();
        self::validateArrayValues($array, $mapping);

        $mapped = [];
        foreach($mapping as $key => $index) {
            $mapped[$index] = $array[$key];
        }

        return new self(...$mapped);
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

        $validator = Validator::make($array, self::arrayValidationRules());

        if(! $validator->fails()) return;

        $msg = "";
        $errors = $validator->errors()->toArray();
        foreach($errors as $key => $messages) {
            $msg .= "Validation for $key failed: " . PHP_EOL . implode(PHP_EOL, $messages);
        }

        throw new \InvalidArgumentException($msg);
    }

    /**
     * This method is ment to be overridden by implementing class
     *
     * @return array $rules
     */
    private static function arrayValidationRules()
    {
        return [];
    }
}
