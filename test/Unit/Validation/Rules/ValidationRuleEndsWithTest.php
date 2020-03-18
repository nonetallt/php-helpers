<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleEndsWithTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'ends_with';
    }

    protected function parameters()
    {
        return ['10'];
    }

    protected function expectations()
    {
        return [
            'pass' => ['foo_10', 'foofoo10', '10', 10, '110', 110],
            'fail' => [1, null, -1, '10f', new \Exception('test')]
        ];
    }
}
