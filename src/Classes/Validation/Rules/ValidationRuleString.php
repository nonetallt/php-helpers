<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleString extends ValidationRule
{
    public function validateValue($value)
    {
        return is_string($value);
    }
}
