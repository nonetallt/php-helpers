<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;

class HttpResponseCollection extends Collection
{
    CONST COLLECTION_TYPE = HttpResponse::class;

    public function getExceptions() : HttpRequestExceptionCollection
    {
        $exceptions = new HttpRequestExceptionCollection();
        foreach($this->items as $item) {
            $exceptions = $exceptions->merge($item->getExceptions());
        }
        return $exceptions;
    }
}
