<?php

namespace Nonetallt\Helpers\Internet\Http\Redirections;

use Nonetallt\Helpers\Generic\Collection;

class HttpRedirectionCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpRedirection::class);
    }

    public function toArray() : array
    {
        return $this->map(function($item) {
            return $item->toArray();
        });
    }

    public function getUrlTrace() : array
    {
        return $this->map(function($item) {
            return $item->getTo();
        });
    }
}
