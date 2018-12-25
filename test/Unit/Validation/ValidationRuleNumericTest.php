<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;

class ValidationRuleNumericTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'numeric';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => [1, -1, '1234', 1.234],
            'fail' => [[], null, 'string', new \Exception('asd')]
        ];
    }
}
