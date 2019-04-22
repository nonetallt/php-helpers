<?php

namespace App\Domain\Api;

use Nonetallt\Helpers\Generic\Collection;

class HttpRequestCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpRequest::class);
    }

    public function fromArray(array $items)
    {
        $requests = [];
        foreach($items as $item) {
            $requests[] = HttpRequest::fromArray($item);
        }

        return $requests;
    }
}
