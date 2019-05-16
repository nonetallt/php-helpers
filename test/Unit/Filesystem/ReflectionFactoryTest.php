<?php

namespace Test\Unit\Filesystem;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionFactory;

class ReflectionFactoryTest extends TestCase
{
    public function testFactoryFindsThisClass()
    {
        $factory = new ReflectionFactory(TestCase::class, __DIR__, __NAMESPACE__);
        $this->assertContains('reflection_factory_test', $factory->getAliases());
    }

    public function testFactoryCanMakeReflectionClassByCallingAliasByDefault()
    {
        $factory = new ReflectionFactory(TestCase::class, __DIR__, __NAMESPACE__);
        $this->assertInstanceOf(\ReflectionClass::class, $factory->make('reflection_factory_test'));
    }
}
