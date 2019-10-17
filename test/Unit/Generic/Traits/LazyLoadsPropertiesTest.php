<?php

namespace Test\Unit\Generic;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Generic\Traits\LazyLoadsProperties;

class LazyLoadsPropertiesTest extends TestCase
{
    private $class;

    public function setUp() : void
    {
        parent::setUp();

        $this->class = (new class {
            use LazyLoadsProperties;

            public function lazyLoadFoo()
            {
                return 'foo';
            }

            public function lazyLoadBar()
            {
                return 'bar';
            }
        });
    }

    public function testGetLazyLoadedPropertiesIsEmptyBeforeAnyGetterIsCalled()
    {
        $this->assertCount(0, $this->class->getLazyLoadedProperties());
    }

    public function testGetLazyLoadedPropertiesHasLoadedValueAfterGetterIsCalled()
    {
        $foo = $this->class->getFoo();
        $this->assertEquals(['foo' => 'foo'], $this->class->getLazyLoadedProperties());
    }

    public function testGetterMethodReturnsTheValueSavedByTheLazyLoadMethod()
    {
        $this->assertEquals('foo', $this->class->getFoo());
    }

    public function testForgetPropertyClearsLoadedProperty()
    {
        $foo = $this->class->getFoo();
        $bar = $this->class->getBar();
        $this->class->forgetLazyLoadedProperty('foo');

        $this->assertEquals(['bar' => 'bar'], $this->class->getLazyLoadedProperties());
    }

    public function testForgetPropertiesClearsAllLoadedProperties()
    {
        $foo = $this->class->getFoo();
        $bar = $this->class->getBar();
        $this->class->forgetLazyLoadedProperties();

        $this->assertCount(0, $this->class->getLazyLoadedProperties());
    }
}
