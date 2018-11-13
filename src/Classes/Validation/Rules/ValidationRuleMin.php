<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleMin extends ValidationRule
{
    public function validateValue($value, string $name)
    {
        $min = $this->getParameter(0);

        /* Default to true if value is not a string or array */
        $result = true;

        if(is_string($value)) $result = strlen($value) >= $min;
        if(is_array($value)) $result =  count($value) >= $min;

        return $this->createResult($this, $result, "Value $name must be more than $min in size");
    }
}
