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

    public function testStartsWIthWhitespaceTrue()
    {
        $this->assertTrue(starts_with_whitespace('  asd'));
    }

    public function testStartsWIthWhitespaceFalse()
    {
        $this->assertFalse(starts_with_whitespace('asd'));
    }

    public function testExplodeMultiple()
    {
        $expected = [
            'test1',
            'test2',
            'test3',
            'test4'
        ];
        $this->assertEquals($expected, explode_multiple('test1,test2 test3|test4', ' ', ',', '|'));
    }

    public function testStrRemoveRecurringErrorsWithLongerThanOneCharacterArgument()
    {
        $this->expectExceptionMessage('Given character must be a string with a lenght of 1 character.');
        str_remove_recurring('testi123', '12');
    }

    public function testStrRemoveRecurring()
    {
        $this->assertEquals('te heae me aaa', str_remove_recurring('tee heaee meee aaa', 'e'));
    }
}
