<?php

namespace Nonetallt\Helpers\Validation\Exceptions;

use Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection;

class ValidationExceptionCollection extends ExceptionCollection
{
    CONST COLLECTION_TYPE = ValidationException::class;

    /**
     * Get all exceptions for a field
     *
     */
    public function getExceptionsForField(string $field) : self
    {
        $exceptions = new self();

        foreach($this->items as $exception) {
            if($exception->getValueName() === $field) {
                $exceptions->push($exception);
            }
        }

        return $exceptions;
    }
}
