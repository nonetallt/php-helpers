<?php

namespace Test\Mock;

use Nonetallt\Helpers\Generic\Collection;

class ExceptionCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, \Exception::class);
    }
}
