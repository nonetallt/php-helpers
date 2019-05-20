<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\ValidationRuleResult;

class ValidationRuleArray extends ValidationRule
{
    public function validate($value, string $name) : ValidationRuleResult
    {
        return $this->createResult($this, is_array($value), "Value $name must be an array");
    }
}
