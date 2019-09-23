<?php

namespace Nonetallt\Helpers\Internet\Http\Exceptions;

use Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection;

class HttpRequestResponseExceptionCollection extends ExceptionCollection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpRequestResponseException::class);
    }
}
