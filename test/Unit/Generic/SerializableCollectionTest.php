<?php

namespace Test\Unit\Generic;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Generic\SerializableCollection;

class SerializableCollectionTest extends TestCase
{
    private $collection;

    public function setUp() : void
    {
        parent::setUp();
        $this->collection = new SerializableCollection;
    }

    public function toArray()
    {
        return ['test'];
    }

    public function testCanBeInitialized()
    {
        $this->assertInstanceOf(SerializableCollection::class, $this->collection);
    }

    public function testToArrayReturnsItemsInArray()
    {
        $exception = new \Exception('test');
        $this->collection->push($exception);
        $this->assertEquals([$exception], $this->collection->toArray());
    }

    public function testToArrayWithRecursiveOptionConvertsItemsToArrayIfMethodExists()
    {
        $this->collection->push($this);
        $expected = [['test']];
        $this->assertEquals($expected, $this->collection->toArray(true));
    }
}
