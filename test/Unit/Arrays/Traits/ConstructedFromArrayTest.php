<?php

namespace Test\Unit\Arrays\Traits;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validator;
use Test\Mock\FromArrayMock;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;
use Test\Mock\MockException;
use Test\Mock\ConstructedFromArray\LevelOneMock;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionMethod;
use Test\Mock\ConstructedFromArray\LevelTwoMock;
use Test\Mock\ConstructedFromArray\LevelThreeMock;

class ConstructedFromArrayTest extends TestCase
{
    private $exception;

    public function setUp() : void
    {
        parent::setUp();
        $this->exception = new MockException('foo');
    }

    private function create(array $args, bool $strict = false)
    {
        $data = [];
        foreach($args as $index => $arg) {
            $argNumber = $index + 1;
            $key = "arg$argNumber";
            $data[$key] = $arg;
        }

        return FromArrayMock::fromArray($data, $strict);
    }

    public function testClassCanBeCreated()
    {
        $mock = $this->create([ 1, $this->exception, 'bar' ]);
        $this->assertInstanceOf(FromArrayMock::class, $mock);
    }

    public function testMissingParameterDoesNotThrowExceptionWhenArgHasDefaultValue()
    {
        $mock = $this->create([ 1, $this->exception ]);
        $this->assertInstanceOf(FromArrayMock::class, $mock);
    }

    public function testMissingParameterWithDefaultValueThrowsExceptionWhenStrictIsTrue()
    {
        $this->expectException(MappingException::class);
        $mock = $this->create([ 1, $this->exception ], true);
        $this->assertInstanceOf(FromArrayMock::class, $mock);
    }

    public function testMissingParametersThrowsException()
    {
        $this->expectException(MappingException::class);
        $mock = $this->create([2 => 'bar']);
        $this->assertInstanceOf(FromArrayMock::class, $mock);
    }

    public function testNestedValuesAreConvertedToTheirClasses()
    {
        $exceptionData = [
            'message' => 'test',
            'code' => -1,
            'previous' => new \Exception('foo')
        ];

        $mock = $this->create([1, $exceptionData]);

        /* Assert that the exception data was set correctly for arg2 */
        $this->assertEquals($exceptionData, $mock->getArg(2)->toArray());
    }

    public function testToArraySerializesObjectRecursively()
    {
        $arg1 = 1; 
        $arg2 = $this->exception; 
        $arg3 = 'bar';

        $mock = $this->create([$arg1, $arg2, $arg3]);

        $expected = [
            'args' => [
                'arg1' => $arg1,
                'arg2' => $arg2->toArray(),
                'arg3' => $arg3
            ]
        ];

        $this->assertEquals($expected, $mock->toArray());
    }

    public function testToArrayShowsClearErrorMessageWhenTryingToConstructNestedObjects()
    {
        $array = [
            'name' => 'foo',
            'next' => [
                'name' => 'bar',
                'next' => [
                    /* 'name' => 'baz' */
                ]
            ]
        ];

        $exceptions = [];

        try {
            $mock = LevelOneMock::fromArray($array);
        }
        catch(MappingException $e) {

            while(true) {
                if($e === null) break;
                $exceptions[] = $e->getMessage();
                $e = $e->getPrevious();
            }
        }

        $expected = [];
        foreach([LevelOneMock::class, LevelTwoMock::class] as $class) {
            $signature = (new ReflectionMethod($class, '__construct'))->getSignature();
            $expected[] = "Argument 2 of $signature could not be created from array";
        }
        $expected[] = "Required value is missing for key 'name'";


        $this->assertEquals($expected, $exceptions);
    }
}
