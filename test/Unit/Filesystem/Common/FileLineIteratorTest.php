<?php

namespace Test\Unit\Filesystem\Common;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Common\File;
use Nonetallt\Helpers\Testing\Traits\TestsFiles;

class FileLineIteratorTest extends TestCase
{
    use TestsFiles;

    private $file;

    public function setUp() : void
    {
        parent::setUp();
        $this->file = new File($this->getTestInputPath('10-lines.txt'));
    }

    public function testIteratorIteratesAllLinesInTheFile()
    {
        $lines = [];

        foreach($this->file->getLines() as $line) {
            $lines[] = $line;
        }

        $expected = [];
        for($n = 1; $n <= 10; $n++) {
            $expected[] = "$n" . PHP_EOL;
        }

        $this->assertEquals($expected, $lines);
    }

    public function testGetReturnsAllLinesByDefault()
    {
        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $this->assertEquals($expected, $this->file->getLines()->get());
    }

    public function testGetOffsetSkipsFirstLines()
    {
        $expected = [6, 7, 8, 9, 10];
        $this->assertEquals($expected, $this->file->getLines()->get(5));
    }

    public function testGetLimitLimitsTheReturnedLines()
    {
        $expected = [1, 2, 3, 4, 5];
        $this->assertEquals($expected, $this->file->getLines()->get(0, 5));
    }

    public function testGetOffsetAndLimitWorkTogether()
    {
        $expected = [3, 4, 5];
        $this->assertEquals($expected, $this->file->getLines()->get(2, 3));
    }
}
