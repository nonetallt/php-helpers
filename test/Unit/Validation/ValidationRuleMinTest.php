<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleMin;

class ValidationRuleMinTest extends ValidationRuleTest
{
    protected function ruleClass()
    {
        return ValidationRuleMin::class;
    }

    protected function ruleName()
    {
        return 'min';
    }

    protected function parameters()
    {
        return [3];
    }

    protected function expectations()
    {
        return [
            'pass' => [null, 'aaa', [1, 2, 3]],
            'fail' => ['aa', []]
        ];
    }
}
