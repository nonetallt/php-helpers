<?php

namespace Nonetallt\Helpers\Mapping;

use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Generic\MissingValue;
use Nonetallt\Helpers\Generic\Exceptions\NotFoundException;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterMappingExceptionCollection;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterMappingException;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterValueMappingException;
use Nonetallt\Helpers\Mapping\ParameterMapping;
use Nonetallt\Helpers\Validation\Exceptions\ValidationExceptionCollection;
use Nonetallt\Helpers\Mapping\Exceptions\MethodMappingException;
use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Arrays\Traits\Arrayable;

class ParameterMappingCollection extends Collection
{
    use Arrayable;

    CONST COLLECTION_TYPE = ParameterMapping::class;

    public function getRequiredParameters() : ParameterMappingCollection
    {
        $required = $this->filter(function($mapping) {
            return $mapping->isRequired();
        });

        return new self($required);
    }

    public function validateArray(array $array) : ValidationExceptionCollection
    {
        $exceptions = new ValidationExceptionCollection();

        foreach($this->items as $mapping) {
            $key = $mapping->getName();
            $value = $array[$key] ?? null;
            $validator = $mapping->getValidator();
            $exceptions->merge($validator->validate($key, $value));
        }

        return $exceptions;
    }

    /**
     *
     * Get the mapping that should be used for a given parameter
     *
     * @throws Nonetallt\Helpers\Generic\Exceptions\NotFoundException
     *
     * @param mixed $parameter String name or int parameter position
     *
     * @return Nonetallt\Helpers\Mapping\ParameterMapping $mapping
     *
     */
    public function findParameterMapping($parameter) : ParameterMapping
    {
        if(! is_string($parameter) && ! is_int($parameter)) {
            $given = (new DescribeObject($parameter))->describeType();
            $msg = "Parameter key must be either int position or string name, $given given";
            throw new \InvalidArgumentException($msg);
        }

        foreach($this->items as $mapping) {
            $key = is_string($parameter) ? $mapping->getName() : $mapping->getPosition();

            if($key === $parameter) {
                return $mapping;
            }
        }

        $keyType = is_string($parameter) ? 'name' : 'position';
        $msg = "Mapping could not be found for parameter with $keyType '$parameter'";
        throw new NotFoundException($msg);
    }

    /**
     * @throws Nonetallt\Helpers\Generic\Exceptions\NotFoundException
     */
    private function findParameterValue(array $array, ParameterMapping $mapping)
    {
        $key = $mapping->getName();
        $position = $mapping->getPosition();

        if(array_key_exists($key, $array)) return $array[$key];
        if(array_key_exists($position, $array)) return $array[$position];

        $msg = "Could not find value for either key: '$key' or position '$position'";
        throw new NotFoundException($msg);
    }

    /**
     * @throws Nonetallt\Helpers\Mapping\Exceptions\MethodMappingException
     *
     * @param array $array Array to be used for mapping
     *
     * @param bool $requireDefaultValues Set to true to create exceptions for
     * missing values in given array that have defined a default value
     *
     */
    public function mapArray(array $array, bool $requireDefaultValues = false) : array
    {
        $result = [];
        $exceptions = new ParameterMappingExceptionCollection();

        foreach($this->items as $mapping) {
            /* Catch all parameter mapping exceptions */
            $exceptions->catch(function() use($mapping, $array, $requireDefaultValues, &$result) {
                $value = $this->mapValue($mapping, $array, $requireDefaultValues);
                $result += $this->mapPositions($mapping, $value);
            });
        }

        if(! $exceptions->isEmpty()) {
            throw new MethodMappingException($exceptions);
        }

        return $result;
    }

    /**
     * Map the given value to correct arg position
     *
     */
    private function mapPositions(ParameterMapping $mapping, $value) : array
    {
        /* Do not try map missing values */
        if(is_a($value, MissingValue::class)) {
            return [];
        }

        $position = $mapping->getPosition();

        /* If arg is variadic, occupy positions beginning from arg position */
        if($mapping->getReflection()->isVariadic() && is_array($value)) {
            $result = [];
            foreach($value as $itemValue) {
                $result[$position] = $itemValue;
                $position++;
            }
            return $result;
        }

        return [$position => $value];
    }

    /**
     * Map a single parameter 
     *
     * @throws Nonetallt\Helpers\Mapping\Exceptions\ParameterMappingException 
     *
     */
    private function mapValue(ParameterMapping $mapping, array $array, bool $requireDefaultValues)
    {
        $value = null;

        /* Find value for mapping */
        try {
            $value = $this->findParameterValue($array, $mapping);
        }
        catch(NotFoundException $e) {
            if($mapping->isRequired() || $requireDefaultValues) {
                throw $this->requiredValueMissing($mapping, $e);
            } 

            /* Can return Nonetallt\Helpers\Generic\MissingValue */
            return $mapping->getDefaultValue();
        }

        /* Add validation exceptions */
        $result = $mapping->getValidator()->validate($mapping->getName(), $value);

        if($result->failed()) {
            $msg = (string)$result->getExceptions();
            throw new ParameterValueMappingException($mapping, $value, $msg);
        }

        return $value;
    }

    protected function requiredValueMissing(ParameterMapping $mapping, \Exception $previous) : ParameterMappingException
    {
        $name = $mapping->getName();
        $msg = "Required value is missing for key '$name'";
        return new ParameterMappingException($mapping, $msg, 0, $previous);
    }
}
