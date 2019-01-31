<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Templating\PlaceholderFormat;

class IsStrConvertableTest extends TestCase
{
    public function testStringIsConvertable()
    {
        $this->assertTrue(is_str_convertable('asd'));
    }

    public function testIntegerIsConvertable()
    {
        $this->assertTrue(is_str_convertable(1));
    }

    public function testFloatIsConvertable()
    {
        $this->assertTrue(is_str_convertable(1.1));
    }

    public function testNullIsConvertable()
    {
        $this->assertTrue(is_str_convertable(null));
    }

    public function testTrueIsConvertable()
    {
        $this->assertTrue(is_str_convertable(true));
    }

    public function testFalseIsConvertable()
    {
        $this->assertTrue(is_str_convertable(false));
    }

    public function testObjectWithToStringIsConvertable()
    {
        $obj = new PlaceholderFormat('{{$}}');
        $this->assertTrue(is_str_convertable($obj));
    }

    public function testArrayIsNotConvertable()
    {
        $this->assertFalse(is_str_convertable([]));
    }

    public function testObjectWithoutToStringIsNotConvertable()
    {
        $this->assertFalse(is_str_convertable($this));
    }
}

