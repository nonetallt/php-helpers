<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\ValidationResult;

class ValidationRuleNumeric extends ValidationRule
{

    public function validate($value, string $name) : ValidationResult
    {
        return $this->createResult($this, is_numeric($value), "Value $name must be numeric");
    }
}
