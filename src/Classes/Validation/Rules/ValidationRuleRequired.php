<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleRequired extends ValidationRule
{
    public function validateValue($value, string $name)
    {
        return $this->createResult($this, ! is_null($value), "Value $name is required");
    }
}
