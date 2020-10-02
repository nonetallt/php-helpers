<?php

namespace Test\Unit\Filesystem\Common;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Common\File;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotFileException;
use Nonetallt\Helpers\Filesystem\Exceptions\PermissionException;
use Nonetallt\Helpers\Testing\Traits\TestsFiles;

class FileModificationTest extends TestCase
{
    use TestsFiles;

    private $input;

    public function setUp() : void
    {
        parent::setUp();

        $filepath = $this->getTestOutputPath('source.txt');
        file_put_contents($filepath, 'foo');

        $this->input = new File($filepath);
    }

    public function testCopyCreatesFileWithCopiedContent()
    {
        $output = $this->input->copy($this->getTestOutputPath('10-lines.txt'));
        $this->assertEquals($this->input->getContent(), $output->getContent());
    }

    public function testMoveRemovesOldFile()
    {
        $oldPath = $this->input->getPath();
        $this->input->move($this->getTestOutputPath('new.txt'));

        $this->assertFalse(file_exists($oldPath));
    }

    public function testMoveCreatesNewFile()
    {
        $this->input->move($this->getTestOutputPath('new.txt'));
        $this->assertTrue(file_exists($this->input->getPath()));
    }

    public function testRenameRemovesOldFile()
    {
        $oldPath = $this->input->getPath();
        $this->input->rename('new.txt');

        $this->assertFalse(file_exists($oldPath));
    }

    public function testRenameCreatesNewFile()
    {
        $output = $this->input->rename('new.txt');
        $this->assertTrue(file_exists($this->input->getPath()));
    }

    public function testWriteString()
    {
        $file = new File($this->getTestInputPath('10-lines.txt'));
        $this->input->write($file->getContent());

        $this->assertEquals($file->getContent(), $this->input->getContent());
    }

    public function testWriteLines()
    {
        $file = new File($this->getTestInputPath('10-lines.txt'));
        $this->input->write($file->getLines());

        $this->assertEquals($file->getContent(), $this->input->getContent());
    }
}
