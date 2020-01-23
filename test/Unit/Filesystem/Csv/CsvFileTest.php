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

    public function setUp() : void
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

    public function testGetHeadersRenamesDuplicateHeadersWhenArgIsTrue()
    {
        $path = $this->getTestInputPath('csv/duplicate_headers.csv');
        $file = new CsvFile($path);

        $this->assertEquals(['first_name1', 'first_name2', 'last_name'], $file->getHeaders(true));
    }

    public function testGetHeadersRenamesEmptyHeadersWhenArgIsTrue()
    {
        $path = $this->getTestInputPath('csv/unnamed_headers.csv');
        $file = new CsvFile($path);

        $this->assertEquals(['first_name', 'unnamed', 'last_name'], $file->getHeaders(false, 'unnamed'));
    }

    public function testGetHeadersCanRenameDuplicateUnnamedHeaders()
    {
        $path = $this->getTestInputPath('csv/duplicate_unnamed_headers.csv');
        $file = new CsvFile($path);

        $this->assertEquals(['first_name', 'unnamed1', 'unnamed2'], $file->getHeaders(true, 'unnamed'));
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
