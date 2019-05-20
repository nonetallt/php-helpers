<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\ValidationRuleResult;

class ValidationRuleRequired extends ValidationRule
{
    public function validate($value, string $name) : ValidationRuleResult
    {
        return $this->createResult($this, $value !== null, "Value $name is required");
    }
}
