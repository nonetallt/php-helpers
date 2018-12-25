<?php

namespace Test\Mock;

class FromArrayMockChild extends FromArrayMockParent
{
    private static function arrayValidationRules()
    {
        return [
            'value1' => 'required|integer',
            'value2' => 'required|integer',
            'value3' => 'required|integer',
        ];
    }
}
