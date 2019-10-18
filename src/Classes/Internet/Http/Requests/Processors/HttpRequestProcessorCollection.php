<?php

namespace Nonetallt\Helpers\Internet\Http\Requests\Processors;

use Nonetallt\Helpers\Generic\Collection;

class HttpRequestProcessorCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpRequestProcessor::class);
    }
}
