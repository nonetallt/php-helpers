<?php

namespace Nonetallt\Helpers\Validation\Results;

use Nonetallt\Helpers\Validation\Exceptions\ValidationExceptionCollection;

class ValidationResult
{
    private $exceptions;

    public function __construct(ValidationExceptionCollection $exceptions = null)
    {
        $this->setExceptions($exceptions);
    }

    public function setExceptions(?ValidationExceptionCollection $exceptions)
    {
        if($exceptions === null) {
            $exceptions = new ValidationExceptionCollection();
        }

        $this->exceptions = $exceptions;
    }

    public function getExceptions() : ValidationExceptionCollection
    {
        return $this->exceptions;
    }

    public function passed() : bool
    {
        return $this->exceptions->isEmpty();
    }

    public function failed() : bool
    {
        return ! $this->exceptions->isEmpty();
    }
}
