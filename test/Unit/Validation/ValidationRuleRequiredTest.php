<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;

class ValidationRuleRequiredTest extends ValidationRuleTest
{
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
