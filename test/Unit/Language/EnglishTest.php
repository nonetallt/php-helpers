<?php

namespace Test\Unit\Language;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Strings\Language\English;

class EnglishTest extends TestCase
{
    public function testArticleReturnsAForWordStartingWithConsonant()
    {
        $this->assertEquals('a', English::article('football'));
    }

    public function testArticleReturnsAnForWordStartingWithVowel()
    {
        $this->assertEquals('an', English::article('alligator'));
    }

    public function testListWordsWithOneItem()
    {
        $expected = 'a foo';
        $this->assertEquals($expected, English::listWords('foo'));
    }

    public function testListWordsWithTwoItems()
    {
        $expected = 'a foo and bar';
        $this->assertEquals($expected, English::listWords('foo', 'bar'));
    }

    public function testListWordsWithThreeItems()
    {
        $expected = 'a foo, bar and baz';
        $this->assertEquals($expected, English::listWords('foo', 'bar', 'baz'));
    }

    public function testListWordsWithFiveItems()
    {
        $expected = 'an apple, orange, banana, kiwi and pineapple';

        $fruits = [
            'apple',
            'orange',
            'banana',
            'kiwi',
            'pineapple'
        ];

        $this->assertEquals($expected, English::listWords(...$fruits));
    }
}
