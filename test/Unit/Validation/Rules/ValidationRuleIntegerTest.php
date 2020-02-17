<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleIntegerTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'integer';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => [1, -1, 1234],
            'fail' => ['string', null, [], new \Exception('test'), 1.234]
        ];
    }
}
