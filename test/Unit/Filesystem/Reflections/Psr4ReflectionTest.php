<?php

namespace Test\Unit\Filesystem\Reflections;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Reflections\Psr4Reflection;
use Test\Mock\FromArrayMock;
use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

class Psr4ReflectionTest extends TestCase
{
    public function testCanBeInitialized()
    {
        $this->assertInstanceOf(\ReflectionClass::class, new Psr4Reflection($this));
    }

    /**
     * NOTE will break if this test is moved up or down the directory structure
     */
    public function testGetNamespaceRoot()
    {
        $ref = new Psr4Reflection($this);

        $expected = dirname(dirname(dirname(__DIR__)));
        $this->assertEquals($expected, $ref->getNamespaceRoot());
    }

    public function testGetTraitsFindsUsedTrait()
    {
        $ref = new Psr4Reflection(FromArrayMock::class);
        $this->assertContains(ConstructedFromArray::class, $ref->getTraits());
    }
}
