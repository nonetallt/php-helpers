<?php

namespace Nonetallt\Helpers\Internet\Http\Exceptions;

/**
 * Catch all for exceptions when sending http requests
 */
class HttpRequestException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
