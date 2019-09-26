<?php

namespace Nonetallt\Helpers\Mapping\Exceptions;

use Nonetallt\Helpers\Mapping\ParameterMapping;

/**
 * Throw when parameter could not be mapped
 */
class ParameterMappingException extends MappingException
{
    private $mapping;

    public function __construct(ParameterMapping $mapping, string $message, int $code = 0, \Exception $previous = null)
    {
        $this->mapping = $mapping;
        parent::__construct($message, $code, $previous);
    }   

    public function getMapping() : ParameterMapping
    {
        return $this->mapping;
    }
}
