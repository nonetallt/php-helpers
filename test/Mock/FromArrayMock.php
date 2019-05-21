<?php

namespace Test\Mock;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

class FromArrayMock
{
    use ConstructedFromArray;

    private $value1;
    private $value2;
    private $value3;

    public function __construct(int $value1, int $value2, int $value3)
    {
        $this->value1 = $value1;
        $this->value2 = $value2;
        $this->value3 = $value3;
    }

    public function getValue1()
    {
        return $this->value1;
    }

    public function getValue2()
    {
        return $this->value2;
    }

    public function getValue3()
    {
        return $this->value3;
    }

    public function toArray()
    {
        return [
            'value1' => $this->value1,
            'value2' => $this->value2,
            'value3' => $this->value3,
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
