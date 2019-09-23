<?php

namespace Nonetallt\Helpers\Laravel\Api\Exception;

use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;

class LaravelApiExceptionFactory extends HttpRequestResponseExceptionFactory
{
    /**
     * @override
     */
    protected function handleArrayException(array $exceptionData, $exceptions)
    {
        foreach($exceptionData as $validatedValue => $validationErrors) {
            foreach($validationErrors as $validationError) {
                $this->handleStringException($validationError, $exceptions);
            }
        }
    }
}
