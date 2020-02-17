<?php

namespace Test\Unit\Validation\Rules;

use Nonetallt\Helpers\Generic\MissingValue;

class ValidationRuleRequiredTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'required';
    }

    protected function parameters()
    {
        return [];
    }

    protected function expectations()
    {
        return [
            'pass' => [1, -3, 'Kappa', [], null],
            'fail' => [new MissingValue]
        ];
    }
}
