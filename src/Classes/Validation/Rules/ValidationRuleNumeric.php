<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleNumeric extends ValidationRule
{
    public function validateValue($value, string $name)
    {
        return $this->createResult($this, is_numeric($value), "Value $name must be numeric");
    }
}
