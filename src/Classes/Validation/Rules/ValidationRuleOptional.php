<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;
use Nonetallt\Helpers\Generic\MissingValue;

class ValidationRuleOptional extends ValidationRule
{
    public function validate($value, string $name) : ValidationRuleResult
    {
        /* If value is optional, null values should not continue validation */
        if($value instanceof MissingValue) {
            return $this->createResult($this, true, '', false);
        }

        /* If value is not null, validation should continue like normal */
        return $this->createResult($this, true, '', true);
    }
}
