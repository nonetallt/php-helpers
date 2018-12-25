<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleArray extends ValidationRule
{
    public function validateValue($value, string $name)
    {
        return $this->createResult($this, is_array($value), "Value $name must be an array");
    }
}
