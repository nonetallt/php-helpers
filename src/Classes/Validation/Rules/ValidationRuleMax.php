<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;

class ValidationRuleMax extends ValidationRule
{
    public function defineParameters()
    {
        return [
            [
                'name' => 'max',
                'type' => 'numeric',
                'is_required' => true
            ]
        ];
    }

    public function validate($value, string $name) : ValidationRuleResult
    {
        $max = $this->parameters['max'];

        /* Default to true if value is not a string or array */
        $result = true;

        if(is_string($value)) $result = strlen($value) <= $max;
        if(is_array($value)) $result =  count($value) <= $max;

        return $this->createResult($this, $result, "Value $name must be no more than $max in size");
    }
}
