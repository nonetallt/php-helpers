<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleArray;

class ValidationRuleArrayTest extends ValidationRuleTest
{
    protected function ruleClass()
    {
        return ValidationRuleArray::class;
    }

    protected function ruleName()
    {
        return 'array';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => [[], [1, 2, 3]],
            'fail' => [1, null, -1, 'string', new \Exception('test')]
        ];
    }
}
