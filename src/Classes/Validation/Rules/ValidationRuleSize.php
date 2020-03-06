<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;

class ValidationRuleSize extends ValidationRule
{
    public function defineParameters()
    {
        return [
            [
                'name' => 'size',
                'type' => 'numeric',
                'is_required' => true
            ]
        ];
    }

    public function validate($value, string $name) : ValidationRuleResult
    {
        $valueSize = null;

        if(is_string($value)) {
            $valueSize = strlen($value);
        }
        else if(is_integer($value) || is_float($value)) {
            $valueSize = $value;
        }
        else if(is_countable($value)) {
            $valueSize = count($value);
        }

        $size = $this->parameters->size;
        $passes =  $valueSize == $size;

        return $this->createResult($this, $passes, "Value $name must have size of $size");
    }
}
