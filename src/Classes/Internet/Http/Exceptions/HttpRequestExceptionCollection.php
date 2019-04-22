<?php

namespace Nonetallt\Helpers\Internet\Http\Exceptions;

use Nonetallt\Helpers\Generic\Collection;

class HttpRequestExceptionCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpRequestException::class);
    }

    public function getMessages() : array
    {
        return $this->map(function($e) {
            return $e->getMessage();
        });
    }
}
