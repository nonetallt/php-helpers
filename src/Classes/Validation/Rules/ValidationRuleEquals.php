<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;
use Nonetallt\Helpers\Generic\MissingValue;

class ValidationRuleEquals extends ValidationRule
{
    public function defineParameters()
    {
        return [
            [
                'name' => 'other',
                'type' => 'string',
                'is_required' => true
            ],
            [
                'name' => 'strict',
                'type' => 'boolean',
                'is_required' => false
            ]
        ];
    }

    public function validate($value, string $name) : ValidationRuleResult
    {
        $other = $this->parameters->other;
        $strict = $this->parameters->strict ?? true;
        $passed = $strict ? $value === $other : $value == $other;

        return $this->createResult($this, $passed, "Value $name must be equal to $other", $passed);
    }
}
