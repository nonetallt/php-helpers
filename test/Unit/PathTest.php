<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Testing\Traits\CleansOutput;
use Nonetallt\Helpers\Filesystem\Path;

class PathTest extends TestCase
{
    use CleansOutput;

    private $testFolder;
    private $outputFolder;

    public function setUp()
    {
        $this->testFolder = dirname(__DIR__);
        $this->outputFolder = $this->testFolder . '/output';
        $this->cleanOutput($this->outputFolder);
    }

    public function testOutputFolderDoesNotExistByDefault()
    {
        $this->assertFalse(Path::exists($this->outputFolder));
    }

    public function testOutputFolderExistsAfterBeingCreated()
    {
        Path::create($this->outputFolder);
        $this->assertTrue(Path::exists($this->outputFolder));
    }

    public function testPathCreateCanBeUsedToCreateMultilevelPaths()
    {
        $path = "$this->outputFolder/level1/level2";
        Path::create($path);
        $this->assertTrue(Path::exists($path));;
    }
}
