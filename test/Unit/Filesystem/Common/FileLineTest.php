<?php

namespace Test\Unit\Filesystem\Common;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Common\File;
use Nonetallt\Helpers\Filesystem\Common\FileLine;
use Nonetallt\Helpers\Testing\Traits\TestsFiles;

class FileLineTest extends TestCase
{
    use TestsFiles;

    private $file;

    public function setUp() : void
    {
        parent::setUp();

        $file = new File($this->getTestInputPath('10-lines.txt'));

        $this->file = new File($this->getTestOutputPath('10-lines.txt'));
        $file->copy($this->file->getPath());
    }

    public function test()
    {
        foreach($this->file as $index => $line) {
            dd('asd');
            dd($line->writeContent('foo'));
            dd($line->getNumber());
            echo ($line);
        }
    }
}
