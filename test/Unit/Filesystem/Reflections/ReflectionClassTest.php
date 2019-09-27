<?php

namespace Test\Unit\Filesystem\Reflections;

use PHPUnit\Framework\TestCase;
use Test\Mock\FromArrayMock;
use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClass;

class ReflectionClassTest extends TestCase
{
    public function testCanBeInitialized()
    {
        $this->assertInstanceOf(\ReflectionClass::class, new ReflectionClass($this));
    }

    /**
     * NOTE will break if this test is moved up or down the directory structure
     */
    public function testGetNamespaceRoot()
    {
        $ref = new ReflectionClass($this);

        $expected = dirname(dirname(dirname(__DIR__)));
        $this->assertEquals($expected, $ref->getPsr4NamespaceRoot());
    }

    public function testGetTraitsFindsUsedTrait()
    {
        $ref = new ReflectionClass(FromArrayMock::class);
        $this->assertContains(ConstructedFromArray::class, $ref->getTraits());
    }
}
