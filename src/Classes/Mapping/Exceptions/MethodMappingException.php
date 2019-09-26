<?php

namespace Nonetallt\Helpers\Mapping\Exceptions;

class MethodMappingException extends MappingException
{
    private $excetpions;

    public function __construct(ParameterMappingExceptionCollection $exceptions, int $code = 0, \Exception $previous = null)
    {
        $this->exceptions = $exceptions;
        parent::__construct((string)$exceptions, $code, $previous);
    }

    public function getParameterExceptions() : ParameterMappingExceptionCollection
    {
        return $this->exceptions;
    }
}
