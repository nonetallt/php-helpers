<?php

namespace Nonetallt\Helpers\Mapping;

use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Generic\MissingValue;
use Nonetallt\Helpers\Generic\Exceptions\NotFoundException;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterMappingExceptionCollection;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterMappingException;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterValueMappingException;
use Nonetallt\Helpers\Mapping\ParameterMapping;
use Nonetallt\Helpers\Validation\Exceptions\ValidationExceptionCollection;

class ParameterMappingCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, ParameterMapping::class);
    }

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
     * @throws Nonetallt\Helpers\Mapping\Exceptions\MappingException
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
                if(! is_a($value, MissingValue::class)) $result[$mapping->getPosition()] = $value;
            });
        }

        if(! $exceptions->isEmpty()) {
            $msg = (string)$exceptions;
            throw new MappingException($msg);
        }

        return $result;
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

            return new MissingValue;
        }

        /* Add validation exceptions */
        $exceptions = $mapping->getValidator()->validate($mapping->getName(), $value);

        if(! $exceptions->isEmpty()) {
            $msg = (string)$exceptions;
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
