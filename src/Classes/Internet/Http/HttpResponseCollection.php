<?php

namespace App\Domain\Api;

use Nonetallt\Helpers\Generic\Collection;

class HttpResponseCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpResponse::class);
    }
}
