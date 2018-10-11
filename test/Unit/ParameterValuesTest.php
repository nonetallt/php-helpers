<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Parameters\ParameterValues;

class ParameterValuesTest extends TestCase
{
    
    public function testCanBeCreatedFromArray()
    {
        $values = ParameterValues::fromArray([
            'value1' => 'one',
            'value2' => 'two',
            'value3' => 'three'
        ]);

        $this->assertInstanceOf(ParameterValues::class, $values);
    }
}
