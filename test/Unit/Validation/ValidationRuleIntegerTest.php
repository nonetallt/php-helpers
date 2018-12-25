<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleInteger;

class ValidationRuleIntegerTest extends ValidationRuleTest
{
    protected function ruleClass()
    {
        return ValidationRuleInteger::class;
    }

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
            'fail' => ['string', null, [], new \Exception('test')]
        ];
    }
}
