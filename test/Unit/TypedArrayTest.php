<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Arrays\TypedArray;

class TypedArrayTest extends TestCase
{
    public function testCanInstantiate()
    {
        $original = [1, 2, 3];
        $array = TypedArray::create('integer', $original);
        $this->assertEquals($original, $array);
    }

    public function testCannotCreateTypedArrayThatDefinesPrimitiveWithAnotherPrimitives()
    {
        $this->expectExceptionMessage('Typed array must be constructed from integer values, string given');

        $original = [1, 2, '3'];
        $array = TypedArray::create('integer', $original);
    }
}
