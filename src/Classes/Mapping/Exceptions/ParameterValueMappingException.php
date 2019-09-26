<?php

namespace Nonetallt\Helpers\Mapping\Exceptions;

use Nonetallt\Helpers\Mapping\ParameterMapping;

/**
 * Throw when a value could not be mapped to a paramter
 *
 */
class ParameterValueMappingException extends ParameterMappingException
{
    private $value;

    public function __construct(ParameterMapping $mapping, $value, string $message, int $code = 0, \Exception $previous = null)
    {
        $this->value = $value;
        parent::__construct($mapping, $message, $code, $previous);
    }   

    public function getValue()
    {
        return $this->value;
    }
}
