<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\ValidationResult;

class ValidationRuleObject extends ValidationRule
{

    public function validate($value, string $name) : ValidationResult
    {
        return $this->createResult($this, is_object($value), "Value $name must be an object");
    }
}
