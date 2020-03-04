<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleUrlParametersTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'url';
    }

    protected function parameters()
    {
        return [
            'require_path' => true,
            'require_query' => true
        ];
    }

    protected function expectations()
    {
        return [
            'pass' => [
                'http://google.com/foo?t=1',
                'https://google.com/foo?t=1',
                'http://127.0.0.1/foo?t1'
            ],
            'fail' => [
                'http://google.com/foo',
                'https://google.com',
                'http://127.0.0.1',
                'http://google.com?t=1',
                'https://google.com?t=1',
                'http://127.0.0.1?t=1',
                'http://google.com/foo',
                'https://google.com/foo',
                'http://127.0.0.1/foo',
            ]
        ];
    }
}
