<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;
use Nonetallt\Helpers\Generic\MissingValue;

class ValidationRuleNullable extends ValidationRule
{
    public function validate($value, string $name) : ValidationRuleResult
    {
        $continue = $value === null ? false : true;

        $msg = "Value $name must be null";
        return $this->createResult($this, ! ($value instanceof MissingValue), $msg, $continue);
    }
}
