<?php

namespace Test\Unit\Arrays\Traits;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validator;
use Test\Mock\FromArrayMockNew;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;

class ConstructedFromArrayNewTest extends TestCase
{
    public function testClassCanBeCreated()
    {
        $mock = FromArrayMockNew::fromArray([
            'arg1' => 1,
            'arg2' => new \Exception('foo'),
            'arg3' => 'bar'
        ]);

        $this->assertInstanceOf(FromArrayMockNew::class, $mock);
    }

    public function testMissingParameterDoesNotThrowExceptionWhenArgHasDefaultValue()
    {
        $mock = FromArrayMockNew::fromArray([
            'arg1' => 1,
            'arg2' => new \Exception('foo'),
            /* 'arg3' => 'bar' */
        ]);

        $this->assertInstanceOf(FromArrayMockNew::class, $mock);
    }

    public function testMissingParameterWithDefaultValueThrowsExceptionWhenStrictIsTrue()
    {
        $this->expectException(MappingException::class);

        $mock = FromArrayMockNew::fromArray([
            'arg1' => 1,
            'arg2' => new \Exception('foo'),
            /* 'arg3' => 'bar' */
        ], true);
    }

    public function testMissingParametersThrowsException()
    {
        $mock = FromArrayMockNew::fromArray([
            /* 'arg1' => 1, */
            /* 'arg2' => new \Exception('foo'), */
            'arg3' => 'bar'
        ]);

        $this->assertInstanceOf(FromArrayMockNew::class, $mock);
    }
}
