<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;

class ValidationRuleNull extends ValidationRule
{
    public function validate($value, string $name) : ValidationRuleResult
    {
        $msg = "Value $name must be null";
        return $this->createResult($this, $value === null, $msg);
    }
}
