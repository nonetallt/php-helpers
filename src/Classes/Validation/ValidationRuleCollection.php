<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Generic\SerializableCollection;

class ValidationRuleCollection extends SerializableCollection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, ValidationRule::class);
    }

    public function getNames() : array
    {
        return $this->map(function($rule) {
            return $rule->getName();
        });
    }
}
