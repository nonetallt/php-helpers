<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Generic\Collection;

class HttpResponseProcessorCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpResponseProcessor::class);
    }

    public function get(string $class)
    {
        return $this->filter(function($item) use($class) {
            return get_class($item) === $class;
        })[0] ?? null;
    }
}
