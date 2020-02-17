<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleScalarTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'scalar';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => ['a', 1, -1],
            'fail' => [new \Exception('asd'), null]
        ];
    }
}
