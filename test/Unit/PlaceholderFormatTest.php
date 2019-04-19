<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Templating\PlaceholderFormat;
use Nonetallt\Helpers\Templating\Exceptions\TemplatingException;

class PlaceholderFormatTest extends TestCase
{
    private $format;

    public function setUp()
    {
        $this->format = new PlaceholderFormat('{{$}}');
    }

    public function testPlaceholderFormatCanBeCreated()
    {
        $this->assertInstanceOf(PlaceholderFormat::class, $this->format);
    }

    public function testExceptionIsThrownWhenStringDoesNotContainDollarSymbol()
    {
        $this->expectException(\InvalidArgumentException::class);
        $placeholder = new PlaceholderFormat('kappa');
    }

    public function testExceptionIsThrownWhenStringHasMoreThanOneDollarSymbol()
    {
        $this->expectException(\InvalidArgumentException::class);
        $placeholder = new PlaceholderFormat('$$');
    }

    public function testGetFormatReturnsTheOriginalString()
    {
        $this->assertEquals('{{$}}', $this->format->getString());
    }

    public function testStartFormatIsParsedCorrectly()
    {
        $this->assertEquals('{{', $this->format->getStart());
    }

    public function testEndFormatIsParsedCorrectly()
    {
        $this->assertEquals('}}', $this->format->getEnd());
    }

    public function testPlaceholderForReturnsKeySurroundedByPlaceholder()
    {
        $this->assertEquals('{{TEST}}', $this->format->getPlaceholderFor('TEST'));
    }

    public function testFindInStringFindsAllPlaceholderValues()
    {
        $expected = [
            '{{placeholder1}}',
            '{{placeholder2}}'
        ];

        $str = 'Kappa23123{{placeholder1}}asdasd345235uih3g3{a}{ { {{placeholder2}}sdasd';

        $this->assertEquals($expected, $this->format->getPlaceholdersInString($str));
    }

    public function testFindPlaceholdersInStringSecondParameterRemovesStartAndEndParenthesis()
    {
        $expected = [
            'placeholder1',
            'placeholder2'
        ];

        $str = 'Kappa23123{{placeholder1}}asdasd345235uih3g3{a}{ { {{placeholder2}}sdasd';

        $this->assertEquals($expected, $this->format->getPlaceholdersInString($str, true));
    }

    public function testFindPlaceholderInStringFindsASinglePlaceholder()
    {
        $expected = ['placeholder'];
        $str = '{{placeholder}}';

        $this->assertEquals($expected, $this->format->getPlaceholdersInString($str, true));
    }

    public function testExceptionIsThrownIfNumberOfStartAndEndingSequencesAreNotEqual()
    {
        $msg = "{{value {{nested}}";
        $this->expectException(TemplatingException::class);
        $placeholder = new PlaceholderFormat('{{$}}');
        $result = $placeholder->getPlaceholdersInString($msg);
    }

    public function testExceptionMessageIsASyntaxErrorWhenNumberOfStartAndEndingSequencesAreNotEqual()
    {
        $msg = "{{value {{nested}}";
        $this->expectExceptionMessage("Syntax error: start count 2 does not match end count 1");
        $placeholder = new PlaceholderFormat('{{$}}');
        $result = $placeholder->getPlaceholdersInString($msg);
    }
    
    public function testFindPlaceholdersInStringFindsNestedPlaceholders()
    {
        $msg = "{{value {{nested}}}}";
        $expected = [
            '{{value {{nested}}}}',
            '{{nested}}'
        ];

        $placeholder = new PlaceholderFormat('{{$}}');
        $result = $placeholder->getPlaceholdersInString($msg);
        $this->assertEquals($expected, $result);
    }

    public function testFindPlaceholdersWorksWithComplexMultilevelStrings()
    {
        $msg = '{{ {{test {{foo|bar}} {{baz}} }} }}';
        $expected = [
            '{{ {{test {{foo|bar}} {{baz}} }} }}',
            '{{test {{foo|bar}} {{baz}} }}',
            '{{foo|bar}}',
            '{{baz}}',
        ];

        $placeholder = new PlaceholderFormat('{{$}}');
        $result = $placeholder->getPlaceholdersInString($msg);
        $this->assertEquals($expected, $result);
    }

    public function testFindPlaceholdersResultCanBeReversed()
    {
        $msg = '{{ {{test {{foo|bar}} {{baz}} }} }}';
        $expected = [
            '{{baz}}',
            '{{foo|bar}}',
            '{{test {{foo|bar}} {{baz}} }}',
            '{{ {{test {{foo|bar}} {{baz}} }} }}',
        ];

        $placeholder = new PlaceholderFormat('{{$}}');
        $result = $placeholder->getPlaceholdersInString($msg, false, true);
        $this->assertEquals($expected, $result);
    }
}
