<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleString extends ValidationRule
{

    public function defineParameters()
    {
        return [
            [
                'name' => 'disallow_numeric',
                'type' => 'bool',
                'is_required' => false
            ]
        ];
    }

    public function validateValue($value, string $name)
    {
        $strict = $this->parameters->disallow_numeric;

        if(! is_null($strict) && $strict) {
            return $this->createResult($this, is_string($value) && ! is_numeric($value), "Value $name must be a non-numeric string");
        }

        return $this->createResult($this, is_string($value), "Value $name must be a string");
    }
}
