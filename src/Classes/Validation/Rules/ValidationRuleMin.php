<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\ValidationRuleResult;

class ValidationRuleMin extends ValidationRule
{
    public function defineParameters()
    {
        return [
            [
                'name' => 'min',
                'type' => 'numeric',
                'is_required' => true
            ]
        ];
    }

    public function validate($value, string $name) : ValidationRuleResult
    {
        $min = $this->parameters['min'];

        /* Default to true if value is not a string or array */
        $result = true;

        if(is_string($value)) $result = strlen($value) >= $min;
        if(is_array($value)) $result =  count($value) >= $min;

        return $this->createResult($this, $result, "Value $name must be more than $min in size");
    }
}
