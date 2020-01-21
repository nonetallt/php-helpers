<?php

namespace Nonetallt\Helpers\Internet\Http\Exceptions\Factory;

use Nonetallt\Helpers\Generic\Exceptions\Factory\ExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestResponseException;
use Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;

class HttpRequestResponseExceptionFactory extends ExceptionFactory
{
    /**
     * @override
     */
    protected function createExceptionCollection() : ExceptionCollection
    {
        return new HttpRequestExceptionCollection();
    }

    protected function handleStringException(string $exceptionData, $exceptions)
    {
        $exceptions->push(new HttpRequestResponseException($exceptionData));
    }

    protected function handleArrayException(array $exceptionData, $exceptions)
    {
        /* Recursively handle nested exceptions */
        foreach($exceptionData as $message) {
            $exceptions->pushAll($this->createExceptions($message));
        }
    }
}
