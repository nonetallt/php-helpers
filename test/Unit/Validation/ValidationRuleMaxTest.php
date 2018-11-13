<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleMax;

class ValidationRuleMaxTest extends ValidationRuleTest
{
    protected function ruleClass()
    {
        return ValidationRuleMax::class;
    }

    protected function ruleName()
    {
        return 'max';
    }

    protected function parameters()
    {
        return [2];
    }

    protected function expectations()
    {
        return [
            'pass' => [null, 'aa', [1]],
            'fail' => ['aaa', [1, 2, 3]]
        ];
    }
}
