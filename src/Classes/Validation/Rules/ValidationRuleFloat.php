<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;

class ValidationRuleFloat extends ValidationRule
{
    public function validate($value, string $name) : ValidationRuleResult
    {
        return $this->createResult($this, is_float($value), "Value $name must be floating point number");
    }
}
