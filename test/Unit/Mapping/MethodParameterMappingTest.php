<?php

namespace Test\Unit\Mapping;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Mapping\MethodParameterMapping;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;

class MethodParameterMappingTest extends TestCase
{
    private $mapping;

    public function setUp() : void
    {
        $reflection = new \ReflectionMethod($this, 'exampleMethod');
        $this->mapping = new MethodParameterMapping($reflection);
    }

    /**
     * Used as test for binding parameters
     */
    private function exampleMethod(string $arg1, ?int $arg2, TestCase $arg3, $arg4 = null)
    {
        if($arg4 === null) return false;
        return true;
    }

    private function assertMissingArgs(array $data, bool $strict, ...$args)
    {
        $expected = [];

        foreach($args as $argNumber) {
            $expected[] = "Required value is missing for key 'arg$argNumber'";
        }

        $expected = implode(PHP_EOL, $expected);
        $this->expectExceptionMessage($expected);
        $parameters = $this->mapping->mapArray($data, $strict);
    }

    public function testExceptionMessageHasEntryForEachMissingParameter()
    {
        $this->assertMissingArgs([], false, 1, 2, 3);
    }

    public function testExceptionMessageHasEntryForMissingDefaultValueIfStrictValidationIsUsed()
    {
        $this->assertMissingArgs([], true, 1, 2, 3, 4);
    }

    public function testExceptionMessageDoesNotIncludeExistingParameters()
    {
        $this->assertMissingArgs(['foo', 1], false, 3);
    }

    public function testOptionalValuesAreSetToDefaultsWhenMissing()
    {
        $parameters = $this->mapping->mapArray([
            'arg1' => 'foo',
            'arg2' => 1,
            'arg3' => $this
        ]);

        $this->assertFalse($this->exampleMethod(...$parameters));
    }

    public function testOptionalValuesAreMappedWhenTheyAreExplicitlySet()
    {
        $parameters = $this->mapping->mapArray([
            'arg1' => 'foo',
            'arg2' => 1,
            'arg3' => $this,
            'arg4' => 'test'
        ]);

        $this->assertTrue($this->exampleMethod(...$parameters));
    }

    public function testNullValueIsAcceptedForNullableArgs()
    {
        $parameters = $this->mapping->mapArray([
            'arg1' => 'foo',
            'arg2' => null,
            'arg3' => $this,
            'arg4' => null
        ]);

        $this->assertFalse($this->exampleMethod(...$parameters));
    }

    public function testExceptionMessageHasEntryForIncorrectParameterTypes()
    {
        $class = TestCase::class;

        $expected = implode(PHP_EOL, [
            'Value arg1 must be a string',
            'Value arg2 must be an integer',
            "Value arg3 must be of type $class",
        ]);

        $this->expectExceptionMessage($expected);
        $this->mapping->mapArray([1, '1', 1, 1]);
    }
}
