<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Parameters\RecursiveAccessor;

class RecursiveAccessorTest extends TestCase
{
    private $accessor;

    public function setUp()
    {
        $this->accessor = new RecursiveAccessor('->');
    }

    public function testCanBeCreated()
    {
        $this->assertInstanceOf(RecursiveAccessor::class, $this->accessor);
    }

    public function testValueContainsDefaultAccessor()
    {
        $this->assertTrue($this->accessor->isContainedInString('test->123'));
    }

    public function testValueDoesNotContainDefaultAccessor()
    {
        $this->assertFalse($this->accessor->isContainedInString('test.123'));
    }
}
