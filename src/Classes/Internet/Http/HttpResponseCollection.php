<?php

namespace Nonetallt\Helpers\Internet\Http;

use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;

class HttpResponseCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpResponse::class);
    }

    public function getExceptions() : HttpRequestExceptionCollection
    {
        $exceptions = new HttpRequestExceptionCollection();
        foreach($this->items as $item) {
            $exceptions = $exceptions->merge($item->getExceptions());
        }
        return $exceptions;
    }
}
