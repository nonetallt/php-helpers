<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;

class HttpResponseProcessorCollection extends Collection
{
    CONST COLLECTION_TYPE = HttpResponseProcessor::class;

    public function __construct(HttpResponseProcessor ...$items)
    {
        parent::__construct($items);
    }
}
