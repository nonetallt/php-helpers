<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleRequired;

class ValidationRuleRequiredTest extends ValidationRuleTest
{
    protected function ruleClass()
    {
        return ValidationRuleRequired::class;
    }

    protected function ruleName()
    {
        return 'required';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => [1, -3, 'Kappa', []],
            'fail' => [null]
        ];
    }
}
