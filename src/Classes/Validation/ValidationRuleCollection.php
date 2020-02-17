<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Generic\SerializableCollection;

class ValidationRuleCollection extends SerializableCollection
{
    CONST COLLECTION_TYPE = ValidationRule::class;

    public function getNames() : array
    {
        return $this->map(function($rule) {
            return $rule->getName();
        });
    }

    public function hasRule(string $name) : bool
    {
        return in_array($name, $this->getNames());
    }
}
