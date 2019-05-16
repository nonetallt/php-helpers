<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\ValidationResult;

class ValidationRuleRequired extends ValidationRule
{
    public function validate($value, string $name) : ValidationResult
    {
        return $this->createResult($this, $value !== null, "Value $name is required");
    }
}
