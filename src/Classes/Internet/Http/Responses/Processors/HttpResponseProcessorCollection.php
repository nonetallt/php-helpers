<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Generic\Collection;

class HttpResponseProcessorCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpResponseProcessor::class);
    }
}
