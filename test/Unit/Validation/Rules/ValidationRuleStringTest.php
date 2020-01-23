<?php

namespace Test\Unit\Validation\Rules;

use Nonetallt\Helpers\Validation\Rules\ValidationRuleString;

class ValidationRuleStringTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'string';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => ['a'],
            'fail' => [1, null, -1, []]
        ];
    }

    public function testOptionalParameterDisallowNumericWorks()
    {
        $rule = new ValidationRuleString(['disallow_numeric' => true]);
        $result = $rule->validate('1234', 'number');

        $this->assertTrue($result->failed());
    }
}
