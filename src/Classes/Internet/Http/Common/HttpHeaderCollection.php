<?php

namespace Nonetallt\Helpers\Internet\Http\Common;

use Nonetallt\Helpers\Generic\Collection;

class HttpHeaderCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpHeader::class);
    }
}
