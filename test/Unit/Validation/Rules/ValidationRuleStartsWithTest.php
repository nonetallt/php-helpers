<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleStartsWithTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'starts_with';
    }

    protected function parameters()
    {
        return ['10'];
    }

    protected function expectations()
    {
        return [
            'pass' => ['10_foo', '10foofoo', '10', 10, '100', 100],
            'fail' => [1, null, -1, 'f10', new \Exception('test')]
        ];
    }
}
