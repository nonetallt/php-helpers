<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleSubclassOf;

class ValidationRuleSubclassOfTest extends ValidationRuleTest
{
    protected function ruleClass()
    {
        return ValidationRuleSubclassOf::class;
    }

    protected function ruleName()
    {
        return 'subclass_of';
    }

    protected function parameters()
    {
        return [\Exception::class];
    }

    protected function expectations()
    {
        return [
            'pass' => [new \InvalidArgumentException('test'), new \LogicException('test')],
            'fail' => [
                1, 
                null, 
                -1, 
                [], 
                /* Expect failure on classname string */ 
                \InvalidArgumentException::class
            ]
        ];
    }
}
