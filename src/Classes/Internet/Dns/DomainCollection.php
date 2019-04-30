<?php

namespace Nonetallt\Helpers\Internet\Dns;

use Nonetallt\Helpers\Internet\Dns\Domain;
use Nonetallt\Helpers\Generic\Collection;

class DomainCollection extends Collection
{
    private $name;

    public function __construct(array $items = [])
    {
        parent::__construct($items, Domain::class);
    }
}
