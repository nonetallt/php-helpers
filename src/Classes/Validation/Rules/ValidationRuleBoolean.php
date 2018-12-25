<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleBoolean extends ValidationRule
{
    public function validateValue($value, string $name)
    {
        return $this->createResult($this, is_bool($value), "Value $name must be boolean");
    }
}
