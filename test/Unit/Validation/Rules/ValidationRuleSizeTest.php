<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleSizeTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'size';
    }

    protected function parameters()
    {
        return [
            'size' => 5
        ];
    }

    protected function expectations()
    {
        return [
            'pass' => [
                /* String of length 5 */
                'xxxxx',
                /* Array with 5 items */
                array_fill(0, 5, 'foo'),
                /* integer 5 */
                5
            ],
            'fail' => [1, null, -1, 'string', new \Exception('test')]
        ];
    }
}
