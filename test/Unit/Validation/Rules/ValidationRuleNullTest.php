<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleNullTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'null';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => [null],
            'fail' => [-1, 'foo', $this, []]
        ];
    }
}
