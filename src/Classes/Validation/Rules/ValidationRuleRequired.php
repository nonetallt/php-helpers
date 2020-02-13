<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;
use Nonetallt\Helpers\Generic\MissingValue;

class ValidationRuleRequired extends ValidationRule
{
    public function validate($value, string $name) : ValidationRuleResult
    {
        /* Validation should continue only if value exists */
        $passed = ! ($value instanceof MissingValue);

        return $this->createResult($this, $passed, "Value $name is required", $passed);
    }
}
