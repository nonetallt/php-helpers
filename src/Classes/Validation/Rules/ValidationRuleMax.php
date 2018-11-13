<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleMax extends ValidationRule
{
    public function validateValue($value)
    {
        $min = $this->getParameter(0);

        if(is_string($value)) return strlen($value) <= $min;
        if(is_array($value)) return count($value) <= $min;

        return true;
    }
}
