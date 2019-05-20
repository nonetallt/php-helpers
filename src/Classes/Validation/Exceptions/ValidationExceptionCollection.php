<?php

namespace Nonetallt\Helpers\Validation\Exceptions;

use Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection;

class ValidationExceptionCollection extends ExceptionCollection
{
    public function __construct(array $items = [])
    {
        parent::__construct();
        $this->setType(ValidationException::class);
        $this->setItems($items);
    }
}
