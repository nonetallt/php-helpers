<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;

class ValidationRuleBooleanTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'boolean';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => [true, false],
            'fail' => [new \Exception('asd'), null, 1, -1, 'string']
        ];
    }
}
