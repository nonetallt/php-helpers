<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;
use Nonetallt\Helpers\Strings\Str;

class ValidationRuleEndsWith extends ValidationRule
{

    public function defineParameters()
    {
        return [
            [
                'name' => 'end',
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
        $end = $this->parameters->end;
        $strict = $this->parameters->strict ?? false;
        $result = false;

        if(! is_string($value) && ! $strict && Str::isConvertable($value)) {
            $value = (string)$value;
        }

        if(is_string($value)) {
            $result = Str::endsWith($value, $end);
        }

        return $this->createResult($this, $result, "Value $name must be a string ending with '$end'");
    }
}
