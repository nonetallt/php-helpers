<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Templating\PlaceholderFormat;

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

    public function testStrSpliceReturnsRemoveString()
    {
        $str = '0123456';
        $this->assertEquals('1234', str_splice($str, 1, 4));
    }

    public function testStrSpliceRemovesSplicedPartFromString()
    {
        $str = '0123456';
        str_splice($str, 1, 4);
        $this->assertEquals('056', $str);
    }

    public function testStrSpliceCanBeUsedWithoutThirdArgument()
    {
        $str = '0123456';
        $this->assertEquals('123456', str_splice($str, 1));
    }

    public function testStrSpliceModifiesSubjectCorrectlyWithoutThridArgument()
    {
        $str = '0123456';
        str_splice($str, 2);
        $this->assertEquals('01', $str);
    }

    public function testStrSliceReturnsCorrectStringOnComplexString()
    {
        $str = '123456789Kappa123456789';
        $this->assertEquals('Kappa', str_splice($str, 9, 5));
    }

    public function testStrSliceModifiesSubjectCorrectlyOnComplexString()
    {
        $str = '123456789Kappa123456789';
        str_splice($str, 9, 5);
        $this->assertEquals('123456789123456789', $str);
    }

    public function testStrAfterReturnStringAfterSpecifiedString()
    {
        $delimiter = "[something]";
        $subject = "before{$delimiter}after";

        $this->assertEquals('after', str_after($subject, $delimiter));
    }

    public function testStrBeforeReturnStringBeforeSpecifiedString()
    {
        $delimiter = "[something]";
        $subject = "before{$delimiter}after";

        $this->assertEquals('before', str_before($subject, $delimiter));
    }

}
