<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleScalar extends ValidationRule
{
    public function validateValue($value, string $name)
    {
        return $this->createResult($this, is_scalar($value), "Value $name must be scalar");
    }
}
