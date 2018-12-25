<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Templating\RecursiveAccessor;

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

    public function testGetNestedValueReturnsValueAtDepth()
    {
        $data = ['level1' => ['level2' => ['level3' => 'level3-value']]];
        $value = $this->accessor->getNestedValue('level1->level2->level3', $data);

        $this->assertEquals('level3-value', $value);
    }

    public function testGetNestedValueThrowsExceptionWhenPathIsEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->accessor->getNestedValue('', ['value' => 1]);
    }

    public function testGetNestedValueThrowsExceptionWhenPathDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->accessor->getNestedValue('undefined', ['value' => 1]);
    }

    public function testIssetReturnsTrueWhenPathIsDefined()
    {
        $data = ['level1' => ['level2' => ['level3' => 'level3-value']]];
        $this->assertTrue($this->accessor->isset('level1->level2->level3', $data));
    }

    public function testIssetReturnsFalseWhenPathIsUndefined()
    {
        $data = ['level1' => ['level2' => ['level3' => 'level3-value']]];
        $this->assertFalse($this->accessor->isset('level1->level2->level4', $data));
    }
}
