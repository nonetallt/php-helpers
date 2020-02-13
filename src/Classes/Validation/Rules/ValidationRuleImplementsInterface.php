<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;

class ValidationRuleImplementsInterface extends ValidationRule
{
    public function defineParameters()
    {
        return [
            [
                'name' => 'interface',
                'type' => 'string',
                'is_required' => true
            ]
        ];
    }

    public function validate($value, string $name) : ValidationRuleResult
    {
        return $this->createResult($this, class_implements($value, $parent), "Value $name must implement interface $parent");
    }
}
