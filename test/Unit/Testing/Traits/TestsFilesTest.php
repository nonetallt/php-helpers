<?php

namespace Test\Unit\Testing\Traits;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Testing\Traits\TestsFiles;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClass;

class TestsFilesTest extends TestCase
{
    use TestsFiles;

    public function testBasePath()
    {
        $ref = new ReflectionClass($this);
        $this->assertEquals(dirname($ref->getPsr4NamespaceRoot()), $this->getBasePath());
    }

    public function testTestPathEqualsBasePathPlusTestDirName()
    {
        $ref = new ReflectionClass($this);
        $this->assertEquals($ref->getPsr4NamespaceRoot(), $this->getTestPath());
    }

    public function testInputPathEqualsTestPathPlusInput()
    {
        $ref = new ReflectionClass($this);
        $this->assertEquals($this->getTestPath('input'), $this->getTestInputPath());
    }

    public function testOutputPathEqualsTestPathPlusOutput()
    {
        $ref = new ReflectionClass($this);
        $this->assertEquals($this->getTestPath('output'), $this->getTestOutputPath());
    }
}
