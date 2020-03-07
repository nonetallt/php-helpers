<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Filesystem\Json\JsonParser;
use Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException;
use Nonetallt\Helpers\Strings\Str;

class JsonParserTest extends TestCase
{
    private function outputPath(string $append = '') : string 
    {
        if(! Str::startsWith($append, '/')) $append = "/$append";
        return dirname(__DIR__)."/input$append";
    }

    private function inputPath(string $append = '') : string
    {
        if(! Str::startsWith($append, '/')) $append = "/$append";
        return dirname(__DIR__)."/output$append";
    }

    public function testValuesAreEncodedUsingJsonEncode()
    {
        $parser = new JsonParser();
        $value = ['foobar'];
        $this->assertEquals(json_encode($value), $parser->encode($value));
    }

    public function testValuesAreDecodedUsingJsonDecode()
    {
        $parser = new JsonParser();
        $value = json_encode(['foobar']);
        $this->assertEquals(json_decode($value), $parser->decode($value));
    }

    public function testDecodeFileCanParseJsonInFileCorrectly()
    {
        $parser = new JsonParser();
        $decoded = $parser->decodeFile($this->inputPath('test.json'));
        $this->assertEquals(['foobar'], $decoded);
    }

    public function testErrorIsThrowWhenTryingToDecodeNonExistentFile()
    {
        $parser = new JsonParser();
        $this->expectException(JsonParsingException::class);
        $parser->decodeFile('foobar');
    }

    public function testErrorIsThrowWhenTryingToDecodeFolder()
    {
        $parser = new JsonParser();
        $this->expectException(JsonParsingException::class);
        $parser->decodeFile(__DIR__);
    }

    public function testEncodeIntoFileCanParseJsonInFileCorrectly()
    {
        $parser = new JsonParser();
        $value = ['foobar'];
        $filepath = $this->outputPath('test.json');
        $decoded = $parser->encodeIntoFile($value, $filepath, true);
        $this->assertEquals(json_encode($value), file_get_contents($filepath));
    }

    public function testErrorIsThrowWhenTryingToEncodeExistingFileWithNoOverride()
    {
        $parser = new JsonParser();
        $filepath = $this->outputPath('test.json');
        $this->expectException(JsonParsingException::class);
        $parser->encodeIntoFile(['foobar'], $filepath);
    }

    public function testErrorIsThrowWhenTryingToEncodeFolder()
    {
        $parser = new JsonParser();
        $filepath = $this->outputPath();
        $this->expectException(JsonParsingException::class);
        $parser->encodeIntoFile(['foobar'], $filepath);
    }

}
