<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleEqualsLaxTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'equals';
    }

    protected function parameters()
    {
        return ['1', false];
    }

    protected function expectations()
    {
        return [
            'pass' => ['1', 1, true],
            'fail' => [false, null, -1, 'true', '-1', new \Exception('test')]
        ];
    }
}
