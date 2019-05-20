<?php

namespace Test\Unit\Mapping;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Mapping\MethodParameterMapping;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;

class MethodParameterMappingTest extends TestCase
{
    public function exampleMethod(string $arg1, int $arg2, TestCase $arg3, $arg4 = null)
    {
        if($arg4 === null) return false;
        return true;
    }

    public function testEmptyArrayFailsValidation()
    {
        $reflection = new \ReflectionMethod($this, 'exampleMethod');
        $mapping = new MethodParameterMapping($reflection);
        $this->expectException(MappingException::class);

        $parameters = $mapping->mapArray([]);
    }

    public function testArrayWithSomeCorrectDataFailsValidation()
    {
        $reflection = new \ReflectionMethod($this, 'exampleMethod');
        $mapping = new MethodParameterMapping($reflection);
        $this->expectException(MappingException::class);

        $parameters = $mapping->mapArray(['foo', 1]);
    }

    public function testArrayWithOnlyCorrectDataPassesValidation()
    {
        $reflection = new \ReflectionMethod($this, 'exampleMethod');
        $mapping = new MethodParameterMapping($reflection);

        $parameters = $mapping->mapArray([
            'arg1' => 'foo',
            'arg2' => 1,
            'arg3' => $this
        ]);

        $this->assertFalse($this->exampleMethod(...$parameters));
    }

    public function testOptionalValuesAreMapped()
    {
        $reflection = new \ReflectionMethod($this, 'exampleMethod');
        $mapping = new MethodParameterMapping($reflection);

        $parameters = $mapping->mapArray([
            'arg1' => 'foo',
            'arg2' => 1,
            'arg3' => $this,
            'arg4' => 'test'
        ]);

        $this->assertTrue($this->exampleMethod(...$parameters));
    }
}
