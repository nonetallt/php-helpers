<?php

namespace Test\Mock;

class FromArrayMockChild extends FromArrayMockParent
{
    private $test;

    public function __construct(string $test)
    {
        $this->test = $test;
        parent::__construct(3, 2, 1);
    }

    protected static function arrayValidationRules()
    {
        return [
            'test' => 'required|string'
        ];
    }
}
