<?php

namespace Test\Unit\Filesystem\Csv;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Common\File;
use Nonetallt\Helpers\Testing\Traits\TestsFiles;
use Nonetallt\Helpers\Filesystem\Csv\CsvFile;

class CsvFileTest extends TestCase
{
    use TestsFiles;

    private $file;

    public function setUp()
    {
        parent::setUp();
        $this->file = new File(__FILE__);
    }

    public function testGetHeadersReturnsFirstLine()
    {
        $path = $this->getTestInputPath('csv/comma_delimiter.csv');
        $file = new CsvFile($path);
        $this->assertEquals(['first_name', 'last_name', 'email'], $file->getHeaders());
    }

    public function testGetHeadersGuessedDelimiter()
    {
        $path = $this->getTestInputPath('csv/semicolon_delimiter.csv');
        $file = new CsvFile($path);
        $this->assertEquals(['first_name', 'last_name', 'email'], $file->getHeaders());
    }

    public function testGuessDelimiterReturnsCommaWhenUsingDefaultArgsAndFileIsCommaDelimited()
    {
        $path = $this->getTestInputPath('csv/comma_delimiter.csv');
        $file = new CsvFile($path);

        $this->assertEquals(',', $file->guessDelimiter());
    }

    public function testGuessDelimiterReturnsSemicolonWhenUsingDefaultArgsAndFileIsSemicolonDelimited()
    {
        $path = $this->getTestInputPath('csv/semicolon_delimiter.csv');
        $file = new CsvFile($path);

        $this->assertEquals(';', $file->guessDelimiter());
    }
}
