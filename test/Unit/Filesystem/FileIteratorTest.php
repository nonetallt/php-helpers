<?php

namespace Test\Unit\Filesystem;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\File;


class FileIteratorTest extends TestCase
{
    private $file;

    public function setUp()
    {
        parent::setUp();
        $this->file = new File(__FILE__);
    }

    public function testIteratorIteratesAllLinesInTheFile()
    {
        $filepath = dirname(dirname(__DIR__)) . '/input/10-lines.txt';
        $file = new File($filepath);
        $lines = [];

        foreach($file->getLines() as $line) {
            $lines[] = $line;
        }

        $expected = [];
        for($n = 1; $n <= 10; $n++) {
            $expected[] = "$n" . PHP_EOL;
        }

        $this->assertEquals($expected, $lines);
    }
}
