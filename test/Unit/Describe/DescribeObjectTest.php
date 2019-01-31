<?php

namespace Test\Unit\Describe;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validator;
use Test\Mock\FromArrayMock;
use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Templating\PlaceholderFormat;

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

    public function testDescribeAsStringReturnsTheSuppliedStringWhenGivenObjectIsString()
    {
        $handle = fopen(__FILE__, 'r');
        
        $expectations = [
            'test'              => 'test',
            'boolean (true)'       => true,
            'boolean (false)'      => false,
            'NULL'              => null,
            self::class         => $this,
            '{{$}}'             => (new PlaceholderFormat('{{$}}')),
            'integer (1)'       => 1,
            'float (1.1)'       => 1.1,
            'resource (stream)' => $handle,
        ];

        foreach($expectations as $expected => $given) {
            $desc = new DescribeObject($given);
            $this->assertEquals($expected, $desc->describeAsString(true));
        }

        fclose($handle);
    }

    public function testDescribeAsStringCanSerializeArrays()
    {
        $arrayOriginal = [
            'name' => 'test',
            'obj' => (new PlaceholderFormat('{{$}}')),
            'bool' => true
        ];

        $arrayExpected = [
            'name' => 'test',
            'obj' => '{{$}}',
            'bool' => 'boolean (true)'
        ];

        $desc = new DescribeObject($arrayOriginal);
        $this->assertEquals(json_encode($arrayExpected, JSON_PRETTY_PRINT), $desc->describeAsString(true));
    }
}
