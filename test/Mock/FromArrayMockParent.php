<?php

namespace Test\Mock;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

abstract class FromArrayMockParent
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
}
