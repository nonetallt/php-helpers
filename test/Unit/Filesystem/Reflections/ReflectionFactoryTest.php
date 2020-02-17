<?php

namespace Test\Unit\Filesystem\Reflections;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionFactory;
use Test\Mock\ReflectionFactory\ExceptionFactory;
use Test\Mock\ReflectionFactory\ExampleException;

/**
 * Note that the invalid argument tests are relevant since the factory proxies
 * method calls from make() to makeItem() on the subclass instance to allow the
 * subclass to define all required parameters for the make() method
 */
class ReflectionFactoryTest extends TestCase
{
    public function testFactoryFindsThisClass()
    {
        $factory = new ReflectionFactory();
        $factory->loadReflections(__DIR__, __NAMESPACE__, TestCase::class);
        $this->assertContains(get_class($this), $factory->getAliases());
    }

    public function testFactoryCanMakeClass()
    {
        $factory = new ReflectionFactory();
        $factory->loadReflections(__DIR__, __NAMESPACE__, TestCase::class);
        $this->assertInstanceOf(TestCase::class, $factory->make(get_class($this)));
    }

    public function testSubclassCanModifySuffix()
    {
        $factory = new ExceptionFactory();
        $this->assertEquals(['Example'], $factory->getAliases());
    }

    public function testSubclassCanCreateItems()
    {
        $factory = new ExceptionFactory();
        $this->assertEquals(['Example'], $factory->getAliases());
        $exception = $factory->make('Example', 'message');
        $this->assertInstanceOf(ExampleException::class, $exception);
    }

    public function testSubclassMakeThrowsExceptionIfNotEnoughArgumentsArePassed()
    {
        $factory = new ExceptionFactory();
        $this->expectException(\ArgumentCountError::class);
        $exception = $factory->make('Example');
    }

    public function testSubclassMakeShowsCorrectMessageIfNotEnoughArgumentsArePassed()
    {
        $factory = new ExceptionFactory();
        $class = ReflectionFactory::class;
        $file = __FILE__;
        $line = __LINE__ + 3;
        $msg = "Too few arguments to function {$class}::make(), 1 passed in $file on line $line and at least 2 expected";
        try {
            $exception = $factory->make('Example');
        }
        catch(\ArgumentCountError $e) {
            $this->assertEquals($msg, $e->getMessage());
        }
    }

    public function testSubclassMakeThrowsExceptionWithIncorrectArgumentType()
    {
        $factory = new ExceptionFactory();
        $this->expectException(\TypeError::class);
        $exception = $factory->make('Example', 1);
    }

    public function testSubclassMakeShowsCorrectMessageWithIncorrectArgumentType()
    {
        $factory = new ExceptionFactory();
        $class = ReflectionFactory::class;
        $file = __FILE__;
        $line = __LINE__ + 3;
        $msg = "Argument 2 passed to {$class}::make(), must be of the type string, int given, called in $file on line $line";
        try {
            $exception = $factory->make('Example', 1);
        }
        catch(\TypeError $e) {
            $this->assertEquals($msg, $e->getMessage());
        }
    }
}
