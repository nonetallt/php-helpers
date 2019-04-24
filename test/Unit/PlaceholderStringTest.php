<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Templating\PlaceholderString;
use Nonetallt\Helpers\Templating\PlaceholderFormat;

class PlaceholderStringTest extends TestCase
{
    public function testGetDepth()
    {
        $format = new PlaceholderFormat('{{$}}');
        $str = PlaceholderString::fromNestedString('{{ {{t2 {{t3}} }} }}', $format);
        $this->assertEquals(3, $str->getDepth());
    }

    public function testToString()
    {
        $format = new PlaceholderFormat('{{$}}');
        $str = PlaceholderString::fromNestedString('{{ {{t2 {{t3}} }} }}', $format);
        $expected = "{{t3}}\n{{t2 {{t3}} }}\n{{ {{t2 {{t3}} }} }}";
        $this->assertEquals($expected, (string)$str);
    }

    /**
     * @group ne
     */
    public function testLoop()
    {
        $str = new PlaceholderString('{{ {{t2 {{t3}} }} }}');
        $str = new PlaceholderString('{{t2 {{t3}} }}', $str);
        $str = new PlaceholderString('{{t3}}', $str);

        $expected = [
            '{{t3}}',
            '{{t2 {{t3}} }}',
            '{{ {{t2 {{t3}} }} }}',
        ];

        $result = $str->loop(function($string) {
            return $string->getContent();
        });

        $this->assertEquals($expected, $result);
    }
}
