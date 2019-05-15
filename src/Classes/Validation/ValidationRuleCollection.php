<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Generic\Collection;

class ValidationRuleCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, ValidationRule::class);
    }
}
