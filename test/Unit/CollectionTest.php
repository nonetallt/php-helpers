<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Generic\Collection;

class CollectionTest extends TestCase
{
    private $collection;

    public function setUp()
    {
        parent::setUp();
        $this->collection = new Collection;
    }

    public function testCanBeInitialized()
    {
        $this->assertInstanceOf(Collection::class, $this->collection);
    }

    public function testTypeCanBeSetOnce()
    {
        $class = self::class;
        $this->collection->setType($class);
        $this->assertEquals($class, $this->collection->getType());
    }

    public function testTypeCannotBeSetTwice()
    {
        $class = self::class;
        $this->expectExceptionMessage("Can't change type, already set: $class");
        $this->collection->setType($class);
        $this->collection->setType(TestCase::class);
    }

    public function testPushAddsItemsToCollection()
    {
        $this->collection->push('test');
        $this->assertEquals(['test'], $this->collection->toArray());
    }

    public function testPushThrowsExceptionWhenTryingToInsertInorrectType()
    {
        $type = TestCase::class;
        $push = new \Exception();
        $given = get_class($push);

        $this->expectExceptionMessage("Pushed item must be of type $type, $given given");
        $this->collection->setType($type);
        $this->collection->push($push);
    }
}
