<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;

class ValidationRuleIs extends ValidationRule
{
    public function defineParameters()
    {
        return [
            [
                'name' => 'class',
                'type' => 'string',
                'is_required' => true
            ],
            [
                'name' => 'accepts_string',
                'type' => 'boolean',
                'is_required' => false
            ],
        ];
    }

    public function validate($value, string $name) : ValidationRuleResult
    {
        $class = $this->parameters->class;
        $acceptString = $this->parameters->accepts_string ?? false;

        return $this->createResult($this, is_a($value, $class, $acceptString), "Value $name must be of type $class");
    }
}
