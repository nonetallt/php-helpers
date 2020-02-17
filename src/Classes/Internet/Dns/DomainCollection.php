<?php

namespace Nonetallt\Helpers\Internet\Dns;

use Nonetallt\Helpers\Internet\Dns\Domain;
use Nonetallt\Helpers\Generic\Collection;

class DomainCollection extends Collection
{
    CONST COLLECTION_TYPE = Domain::class;

    private $name;

    /**
     * @override
     */
    public function toArray() : array
    {
        $array = [];

        foreach($this->items as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }
}
