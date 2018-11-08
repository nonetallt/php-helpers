<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Parameters\ParameterFactory;

class ParametersTest extends TestCase
{

    public function testToArraySerializesCorrectly()
    {
        $array = [
            [
                'name' => 'test_text',
                'type' => 'text',
                'options' => []
            ],
            [
                'name' => 'test_array',
                'type' => 'text',
                'options' => ['required_keys' => ['key1', 'key2']]
            ]
        ];

        $factory = new ParameterFactory();
        $parameters = $factory->parametersFromArray($array);

        $this->assertEquals($array, $parameters->toArray());
    }
}
