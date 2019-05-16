<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\ValidationResult;

class ValidationRuleOptional extends ValidationRule
{
    public function validate($value, string $name) : ValidationResult
    {
        /* If value is optional, null values should not continue validation */
        if($value === null) {
            return $this->createResult($this, true, '', false);
        }

        /* If value is not null, validation should continue like normal */
        return $this->createResult($this, true, '', true);
    }
}
