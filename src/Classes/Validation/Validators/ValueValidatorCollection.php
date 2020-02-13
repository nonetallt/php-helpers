<?php

namespace Nonetallt\Helpers\Validation\Validators;

use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Validation\Validators\ValueValidator;

class ValueValidatorCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, ValueValidator::class);
    }
}
