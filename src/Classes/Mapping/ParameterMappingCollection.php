<?php

namespace Nonetallt\Helpers\Mapping;

use Nonetallt\Helpers\Mapping\ParameterMapping;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;
use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;

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

    /**
     * @throws Nonetallt\Helpers\Validation\Exceptions\ValidationException
     */
    public function validateArray(array $array)
    {
        foreach($this->items as $mapping) {
            $key = $mapping->getName();
            $value = $array[$key] ?? null;
            $mapping->validateValue($value);
        }
    }

    /**
     * @throws Nonetallt\Helpers\Mapping\Exceptions\MappingException
     */
    public function mapArray(array $array) : array
    {
        $result = [];

        foreach($this->items as $mapping) {
            $key = $mapping->getName();
            $value = $array[$key] ?? null;
            $position = $mapping->getPosition();

            try {
                $mapping->validateValue($value);
                $result[$position] = $value;
            }
            catch(ValidationException $e) {
                $msg = "Value '$key' failed validation and cannot be mapped";
                throw new MappingException($msg, 0, $e);
            }
        }

        return $result;
    }
}
