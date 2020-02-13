<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;

class ValidationRuleIn extends ValidationRule
{
    public function defineParameters()
    {
        return [
        ];
    }

    public function validate($value, string $name) : ValidationRuleResult
    {
        $validChoices = $this->parameters->getData();
        $choices = [];
        foreach($validChoices as $choice) {
            $choices[] = (new DescribeObject($choice))->describeValue();
        }

        $choices = implode(', ', $choices);

        return $this->createResult($this, in_array($value, $validChoices), "Value $name must be one of the following: [$choices]");
    }
}
