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

    public function testCountReturnsZeroWhenThereAreNoItems()
    {
        $this->assertEquals(0, $this->collection->count());
    }

    public function testCountReturnsTheNumberOfItemsInCollection()
    {
        $this->collection->push('test');
        $this->assertEquals(1, $this->collection->count());
    }

    public function testFirstReturnsTheFirstItemInCollection()
    {
        $this->collection->push(1);
        $this->collection->push(2);
        $this->collection->push(3);
        $this->assertEquals(1, $this->collection->first());
    }

    public function testMergeThrowsErrorWhenMergingWithCollectionOfDifferentType()
    {
        $expected = self::class;
        $actual = Collection::class;
        $this->expectExceptionMessage("Can't merge collections of type $expected and $actual");

        $this->collection->setType($expected);
        $col = new Collection([], $actual);
        $this->collection->merge($col);
    }

    public function testMergeCreatesNewCollectionWithElementsFromBothCollections()
    {
        $this->collection->push(1);
        $col = new Collection([2, 3]);
        $this->assertEquals([1, 2, 3], $this->collection->merge($col)->toArray());
    }
}
