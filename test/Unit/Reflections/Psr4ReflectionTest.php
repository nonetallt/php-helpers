<?php

namespace Test\Unit\Reflections;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Reflections\Psr4Reflection;

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

        $expected = dirname(dirname(__DIR__));
        $this->assertEquals($expected, $ref->getNamespaceRoot());
    }
}
