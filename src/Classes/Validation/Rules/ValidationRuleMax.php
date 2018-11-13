<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleMax extends ValidationRule
{
    public function validateValue($value, string $name)
    {
        $max = $this->getParameter(0);

        /* Default to true if value is not a string or array */
        $result = true;

        if(is_string($value)) $result = strlen($value) <= $max;
        if(is_array($value)) $result =  count($value) <= $max;

        return $this->createResult($this, $result, "Value $name must be no more than $max in size");
    }
}
