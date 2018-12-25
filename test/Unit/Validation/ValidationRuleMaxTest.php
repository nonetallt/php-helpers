<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;

class ValidationRuleMaxTest extends ValidationRuleTest
{
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
