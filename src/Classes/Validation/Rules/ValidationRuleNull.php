<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\ValidationResult;

class ValidationRuleNull extends ValidationRule
{
    public function validate($value, string $name) : ValidationResult
    {
        $msg = "Value $name must be null";
        return $this->createResult($this, $value === null, $msg);
    }
}
