<?php

namespace Test\Unit\Describe;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validator;
use Test\Mock\FromArrayMock;
use Nonetallt\Helpers\Describe\DescribeObject;

class DescribeObjectTest extends TestCase
{

    public function testDescribeTypeReturnsTypeWhenVariableIsScalar()
    {
        $desc = new DescribeObject('test string');
        $this->assertEquals('string', $desc->describeType());
    }

    public function testDescribeTypeReturnsClassWhenVariableIsObject()
    {
        $desc = new DescribeObject(FromArrayMock::fromArray(['value1' => 1, 'value2' => 2, 'value3' => 3]));
        $this->assertEquals(FromArrayMock::class, $desc->describeType());
    }

    public function testDescibeValueReturnsStringWhenVariableIsString()
    {
        $desc = new DescribeObject('test');
        $this->assertEquals('test', $desc->describeValue());
    }

    public function testDescibeValueReturnsNullStringWhenValueIsNull()
    {
        $desc = new DescribeObject(null);
        $this->assertEquals('null', $desc->describeValue());
    }

    public function testDescibeValueReturnsArrayStringWhenValueIsArray()
    {
        $desc = new DescribeObject([]);
        $this->assertEquals('array', $desc->describeValue());
    }

    public function testDescibeValueReturnsIntegerWhenValueIsInteger()
    {
        $desc = new DescribeObject(1);
        $this->assertEquals('1', $desc->describeValue());
    }

    public function testDescibeValueReturnsBoolean()
    {
        $desc = new DescribeObject(true);
        $this->assertEquals('true', $desc->describeValue());
    }
}
