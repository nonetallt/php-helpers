<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleString extends ValidationRule
{
    public function validateValue($value, string $name)
    {
        return $this->createResult($this, is_string($value), "Value $name must be a string");
    }
}
