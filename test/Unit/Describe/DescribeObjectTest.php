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
}
