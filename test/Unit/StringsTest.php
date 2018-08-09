<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;

class StringsTest extends TestCase
{

    public function testStartsWithReturnsTrueWhenSubjectStartsWithCorrectString()
    {
        $this->assertTrue(starts_with('test123', 'tes'));
    }

    public function testStartsWithReturnsFalseWhenSubjectStartsWithIncorrectString()
    {
        $this->assertFalse(starts_with('test123', 'es'));
    }

    public function testEndsWithReturnsTrueWhenSubjectEndsWithCorrectString()
    {
        $this->assertTrue(ends_with('test123', '123'));
    }

    public function testEndsWithReturnFalseWhenSubjectEndsWithIncorrectString()
    {
        $this->assertFalse(ends_with('test123', '13'));
    }
}
