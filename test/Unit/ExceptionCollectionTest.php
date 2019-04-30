<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection;

class ExceptionCollectionTest extends TestCase
{
    private $collection;

    public function setUp()
    {
        parent::setUp();
        $this->collection = new ExceptionCollection();
    }

    public function testHasExceptionOfClassReturnsFalseIfThereAreNoItemsInCollection()
    {
        $this->assertFalse($this->collection->hasExceptionOfClass(self::class));
    }

    public function testHasExceptionReturnsFalseIfTheSpecifiedExceptionIsNotFound()
    {
        $this->collection->push(new \InvalidArgumentException('test'));
        $this->assertFalse($this->collection->hasExceptionOfClass(self::class));
    }

    public function testHasExceptionReturnsFalseIfTheSpecifiedExceptionIsNotOfExactClassAndSubclassesAreNotAllowed()
    {
        $this->collection->push(new \InvalidArgumentException('test'));
        $this->assertFalse($this->collection->hasExceptionOfClass(\Exception::class, false));
    }

    public function testHasExceptionReturnsTrueIfTheSpecifiedExceptionIsOfExactClass()
    {
        $this->collection->push(new \InvalidArgumentException('test'));
        $this->assertTrue($this->collection->hasExceptionOfClass(\InvalidArgumentException::class, false));
    }

    public function testHasExceptionReturnsTrueIfTheSpecifiedExceptionIsAllowedSubclass()
    {
        $this->collection->push(new \InvalidArgumentException('test'));
        $this->assertTrue($this->collection->hasExceptionOfClass(\Exception::class, true));
    }

    public function testSubclassesAreAcceptedByDefault()
    {
        $this->collection->push(new \InvalidArgumentException('test'));
        $this->assertTrue($this->collection->hasExceptionOfClass(\Exception::class));
    }
}
