<?php

namespace Test\Mock;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

class FromArrayMapMock
{
    use ConstructedFromArray;

    private $value1;
    private $value2;
    private $value3;

    public function __construct($value3, $value2, $value1)
    {
        $this->value1 = $value1;
        $this->value2 = $value2;
        $this->value3 = $value3;
    }

    private static function arrayToConstructorMapping()
    {
        return [
            'value_1' => 'value1',
            'value_2' => 'value2'
        ];
    }
    
    private static function arrayValidationRules()
    {
        return [
            'value1' => 'required|integer',
            'value2' => 'required|integer',
            'value3' => 'required|integer',
        ];
    }
}
