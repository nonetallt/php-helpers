<?php

namespace Nonetallt\Helpers\Internet\Http\Redirections;

use Nonetallt\Helpers\Generic\Collection;

class HttpRedirectionCollection extends Collection
{
    CONST COLLECTION_TYPE = HttpRedirection::class;

    public function getUrlTrace() : array
    {
        return $this->map(function($item) {
            return $item->getTo();
        });
    }

    public function toArray() : array
    {
        return $this->map(function($item) {
            return $item->toArray();
        });
    }
}
