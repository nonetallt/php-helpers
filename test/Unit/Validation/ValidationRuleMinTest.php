<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;

class ValidationRuleMinTest extends ValidationRuleTest
{
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
            'fail' => ['aa', [], [1, 2]]
        ];
    }
}
