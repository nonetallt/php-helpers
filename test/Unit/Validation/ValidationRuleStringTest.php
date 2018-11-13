<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleString;

class ValidationRuleStringTest extends ValidationRuleTest
{
    protected function ruleClass()
    {
        return ValidationRuleString::class;
    }

    protected function ruleName()
    {
        return 'string';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => ['a'],
            'fail' => [1, null, -1, []]
        ];
    }
}
