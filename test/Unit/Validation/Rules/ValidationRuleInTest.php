<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleInTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'in';
    }

    protected function parameters()
    {
        return ['kappa', 'keepo'];
    }

    protected function expectations()
    {
        return [
            'pass' => ['kappa', 'keepo'],
            'fail' => [1, null, -1, 'string', new \Exception('test')]
        ];
    }
}
