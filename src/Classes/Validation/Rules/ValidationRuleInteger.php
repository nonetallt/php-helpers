<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleInteger extends ValidationRule
{
    public function validateValue($value, string $name)
    {
        return $this->createResult($this, is_int($value), "Value $name must be an integer");
    }
}
