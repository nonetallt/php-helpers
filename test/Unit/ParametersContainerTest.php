<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Parameters\ParametersContainer;

class ParametersContainerTest extends TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = new ParametersContainer([
            'value1' => 1,
            'value2' => '{{placeholder2}}',
            'value3' => '{{placeholder3->key1}}',
            'value4' => '{{placeholder4-1}} {{placeholder4-2}}',
        ]);
        parent::setUp();
    }

    public function testObjectCanBeCreated()
    {
        $container = new ParametersContainer(['value']);
        $this->assertInstanceOf(ParametersContainer::class, $container);
    }

    public function testValuesCanBeAccessedWithMagicGet()
    {
        $this->assertEquals(1, $this->container->value1);
    }

    public function testGetThrowsExceptionWhenKeyIsUndefined()
    {
        $this->expectExceptionMessage("Undefined parameter 'value123'");
        $this->container->value123;
    }

    public function testPlaceholderValuesAreReplacedByGet()
    {
        $this->container->setPlaceholderValues(['placeholder2' => 2]);
        $this->assertEquals(2, $this->container->value2);
    }

    public function testValueContainsDefaultAccessor()
    {
        $this->assertTrue($this->container->containsAccessor('test->123'));
    }

    public function testValueDoesNotContainDefaultAccessor()
    {
        $this->assertFalse($this->container->containsAccessor('test.123'));
    }

    /**
     * Used to test that getNestedValues can be used when replacingPlaceholders
     */
    public function testGetNestedValueReturnsValueAtDepth()
    {
        $this->container->setPlaceholderValues(['value4' => ['nested1' => ['nested2' => 4]]]);
        $data = $this->container->getPlaceholderValues();
        $this->assertEquals(4, $this->container->getNestedValue('value4->nested1->nested2', $data));
    }

    public function testAllPlaceholdersAreParsedFromString() 
    {
        $expected = ['placeholder4-1', 'placeholder4-2'];
        $this->assertEquals($expected, $this->container->placeholdersFor('value4'));
    }

    public function testAllPlaceholdersAreReplacedFromString()
    {
        $this->container->setPlaceholderValues(['placeholder4-1' => 41, 'placeholder4-2' => 42]);
        $this->assertEquals('41 42', $this->container->value4);
    }

    public function testNestedPlaceholdersCanBeReplacedViaAccessor()
    {
        /* Define placeholder 3 with nested value */
        $this->container->setPlaceholderValues(['placeholder3' => [
            'key1' => 3
        ]]);

        $this->assertEquals(3, $this->container->value3);
    }

    public function testToArrayHasReplacedPlaceholderValues()
    {
        $expected = [
            'value1' => 1,
            'value2' => 2,
            'value3' => 3,
            'value4' => '41 42',
        ];

        $placeholders = [
            'placeholder2' => 2,
            'placeholder3' => ['key1' => 3],
            'placeholder4-1' => 41,
            'placeholder4-2' => 42
        ];

        $this->container->setPlaceholderValues($placeholders);
        $this->assertEquals($expected, $this->container->toArray());
    }

    public function testGetNestedValueThrowsExceptionWhenPathIsEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->container->getNestedValue('', ['value' => 1]);
    }

    public function testGetNestedValueThrowsExceptionWhenPathDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->container->getNestedValue('undefined', ['value' => 1]);
    }

    public function testToArrayWorksWithMultilevelNesting()
    {
        $placeholders = [
            'event' => [
                'model' => [
                    'hostname' => 'example.com',
                    'entries' => [
                        'example1' => 3,
                        'example2' => 4
                    ]
                ]
            ]
        ];

        $expected = [
            'value1' => 1,
            'value2' => 2,
            'value3' => 3,
        ];

        $container = new ParametersContainer([
            'value1' => 1,
            'value2' => 2,
            'value3' => '{{event->model->entries->example1}}'
        ]);

        $container->setPlaceholderValues($placeholders);
        $this->assertEquals($expected, $container->toArray());
    }

    public function testGetPlaceholdersFindsAllPlaceholders()
    {
        $expected = ['placeholder2', 'placeholder3->key1', 'placeholder4-1', 'placeholder4-2'];
        $placeholders = $this->container->getPlaceholders();
        $this->assertEquals($expected, $placeholders);
    }
}
