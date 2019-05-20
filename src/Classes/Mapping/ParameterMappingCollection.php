<?php

namespace Nonetallt\Helpers\Mapping;

use Nonetallt\Helpers\Mapping\ParameterMapping;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterMappingException;
use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Validation\Exceptions\ValidationExceptionCollection;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterValueMappingException;

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
     * @throws Nonetallt\Helpers\Mapping\Exceptions\ParameterMappingException
     */
    private function getValue(array $array, ParameterMapping $mapping)
    {
        $key = $mapping->getName();
        $position = $mapping->getPosition();

        if(isset($array{$key})) return $array[$key];
        if(isset($array{$position})) return $array[$position];

        $msg = "Could not find value for either key: '$key' or position '$position'";
        throw new ParameterMappingException($mapping, $msg);
    }

    /**
     * @throws Nonetallt\Helpers\Mapping\Exceptions\ParameterMappingException
     */
    public function mapArray(array $array) : array
    {
        $result = [];

        foreach($this->items as $mapping) {
            $value = null;

            /* Skip missing optional values */
            try {
                $value = $this->getValue($array, $mapping);
            }
            catch(ParameterMappingException $e) {
                if($mapping->isRequired()) {
                    throw $this->requiredValueMissing($mapping, $e);
                } 
                continue;
            }

            $exceptions = $mapping->getValidator()->validate($mapping->getName(), $value);

            if(! $exceptions->isEmpty()) {
                $msg = (string)$exceptions;
                throw new ParameterValueMappingException($mapping, $value, $msg);
            }

            $result[$mapping->getPosition()] = $value;
        }

        return $result;
    }

    protected function requiredValueMissing(ParameterMapping $mapping, \Exception $previous) : ParameterMappingException
    {
        $name = $mapping->getName();
        $msg = "Missing mapping for required value '$name'";
        return new ParameterMappingException($mapping, $msg, null, 0, $previous);
    }
}
