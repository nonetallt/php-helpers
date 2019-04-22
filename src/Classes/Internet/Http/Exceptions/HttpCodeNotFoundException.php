<?php

namespace Nonetallt\Helpers\Internet\Http\Exceptions;

class HttpCodeNotFoundException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
