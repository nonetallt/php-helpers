<?php

namespace Test\Unit\Validation\Rules;

class ValidationRuleUrlTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'url';
    }

    protected function parameters()
    {
        return [
            /* 'require_path' => true, */
            /* 'require_query' => true */
        ];
    }

    protected function expectations()
    {
        return [
            'pass' => [
                'http://google.com/foo',
                'https://google.com',
                'http://127.0.0.1'
            ],
            'fail' => [
                '127.0.0.1',
                'localhost',
                'google.com',
                'www.google.com',
                'foo',
                1,
                []
            ]
        ];
    }
}
