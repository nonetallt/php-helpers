<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Strings\Str;

class StrTest extends TestCase
{
    public function testStartsWithReturnsTrueWhenSubjectStartsWithCorrectString()
    {
        $this->assertTrue(Str::startsWith('test123', 'tes'));
    }

    public function testStartsWithReturnsFalseWhenSubjectStartsWithIncorrectString()
    {
        $this->assertFalse(Str::startsWith('test123', 'es'));
    }

    public function testEndsWithReturnsTrueWhenSubjectEndsWithCorrectString()
    {
        $this->assertTrue(Str::endsWith('test123', '123'));
    }

    public function testEndsWithReturnFalseWhenSubjectEndsWithIncorrectString()
    {
        $this->assertFalse(Str::endsWith('test123', '13'));
    }

    public function testStartsWIthWhitespaceTrue()
    {
        $this->assertTrue(Str::startsWithWhitespace('  asd'));
    }

    public function testStartsWIthWhitespaceFalse()
    {
        $this->assertFalse(Str::startsWithWhitespace('asd'));
    }

    public function testExplodeMultiple()
    {
        $expected = [
            'test1',
            'test2',
            'test3',
            'test4'
        ];
        $this->assertEquals($expected, Str::explodeMultiple('test1,test2 test3|test4', ' ', ',', '|'));
    }

    public function testStrRemoveRecurringErrorsWithLongerThanOneCharacterArgument()
    {
        $this->expectExceptionMessage('Given character must be a string with a length of 1 character.');
        Str::removeRecurring('testi123', '12');
    }

    public function testStrRemoveRecurring()
    {
        $this->assertEquals('te heae me aaa', Str::removeRecurring('tee heaee meee aaa', 'e'));
    }

    public function testStrSpliceReturnsRemoveString()
    {
        $str = '0123456';
        $this->assertEquals('1234', Str::splice($str, 1, 4));
    }

    public function testStrSpliceRemovesSplicedPartFromString()
    {
        $str = '0123456';
        Str::splice($str, 1, 4);
        $this->assertEquals('056', $str);
    }

    public function testStrSpliceCanBeUsedWithoutThirdArgument()
    {
        $str = '0123456';
        $this->assertEquals('123456', Str::splice($str, 1));
    }

    public function testStrSpliceModifiesSubjectCorrectlyWithoutThridArgument()
    {
        $str = '0123456';
        Str::splice($str, 2);
        $this->assertEquals('01', $str);
    }

    public function testStrSliceReturnsCorrectStringOnComplexString()
    {
        $str = '123456789Kappa123456789';
        $this->assertEquals('Kappa', Str::splice($str, 9, 5));
    }

    public function testStrSliceModifiesSubjectCorrectlyOnComplexString()
    {
        $str = '123456789Kappa123456789';
        Str::splice($str, 9, 5);
        $this->assertEquals('123456789123456789', $str);
    }

    public function testStrAfterReturnStringAfterSpecifiedString()
    {
        $delimiter = "[something]";
        $subject = "before{$delimiter}after";

        $this->assertEquals('after', Str::after($subject, $delimiter));
    }

    public function testStrBeforeReturnStringBeforeSpecifiedString()
    {
        $delimiter = "[something]";
        $subject = "before{$delimiter}after";

        $this->assertEquals('before', str::before($subject, $delimiter));
    }

    public function testContainsReturnsTrueWhenSubjectContainsTarget()
    {
        $this->assertTrue(Str::contains('foobarbaz', 'bar'));
    }

    public function testContainsReturnsFalseWhenSubjectDoesNotContainTarget()
    {
        $this->assertFalse(Str::contains('foobarbaz', 'fooo'));
    }
}
