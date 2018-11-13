<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleRequired extends ValidationRule
{
    public function validateValue($value)
    {
        return ! is_null($value);
    }
}
