<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validator;
use Test\Mock\FromArrayMock;
use Test\Mock\FromArrayMockChild;
use Test\Mock\FromArrayMapMock;
use Test\Mock\FromArrayDefaultValuesMock;

class ConstructedFromArrayTest extends TestCase
{

    /**
     * Make sure that array value key pairs can be used as constructor
     * parameters using reflection to match array keys to constructor parameter
     * names.
     */
    public function testConstructorValuesCanBeMappedFromArray()
    {
        $data = [
            'value1' => 1,
            'value2' => 2,
            'value3' => 3,
        ];

        $mock = FromArrayMock::fromArray($data);
        $this->assertEquals($data, $mock->toArray());
    }

    /**
     * Make sure that arrayValidationRules method on classes can be used by the
     * trait to validate given the given array.
     */
    public function testMockValidationFailsWhenTryingToUseStringAsValue()
    {
        $msg = "Validation for value1 failed:" . PHP_EOL . "- Value value1 must be an integer";
        $this->expectExceptionMessage($msg);

        $data = [
            'value1' => 'asd',
            'value2' => 2,
            'value3' => 3,
        ];

        $mock = FromArrayMock::fromArray($data);
    }

    public function testMockCanBeCreatedFromSubclassOfAbstracClass()
    {
        $data = [
            'test' => 'test',
        ];

        $mock = FromArrayMockChild::fromArray($data, FromArrayMockChild::class);
        $this->assertInstanceOf(FromArrayMockChild::class, $mock);
    }

    /**
     * Make sure ConstructedFromArray trait static method arrayToConstructorMapping works
     */
    public function testMockCanHaveMappingForKeys()
    {
        $data = [
            'value_1' => 1,
            'value_2' => 2,
            'value3' => 3,
        ];

        $mock = FromArrayMapMock::fromArray($data);
        $this->assertInstanceOf(FromArrayMapMock::class, $mock);
    }

    public function testObjectsCanBeConstructedWithMissingParametersIfMissingParametersHaveDefaultValues()
    {
        $data = [
            'value2' => 2,
            'value3' => 3,
        ];

        $mock = FromArrayDefaultValuesMock::fromArray($data);
        $this->assertInstanceOf(FromArrayDefaultValuesMock::class, $mock);
    }
}
