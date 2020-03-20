<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleEqualsStrictTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'equals';
    }

    protected function parameters()
    {
        return ['1'];
    }

    protected function expectations()
    {
        return [
            'pass' => ['1'],
            'fail' => [1, null, -1, '-1', true, false, new \Exception('test')]
        ];
    }
}
