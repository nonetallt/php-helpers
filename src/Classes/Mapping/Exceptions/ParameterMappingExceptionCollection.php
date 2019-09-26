<?php

namespace Nonetallt\Helpers\Mapping\Exceptions;

use Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterMappingException;

class ParameterMappingExceptionCollection extends ExceptionCollection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, ParameterMappingException::class);
    }
}
