<?php

namespace Nonetallt\Helpers\Validation\Exceptions;

use Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection;

class ValidationExceptionCollection extends ExceptionCollection
{
    CONST COLLECTION_TYPE = ValidationException::class;
}
