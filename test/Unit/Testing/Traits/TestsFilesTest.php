<?php

namespace Test\Unit\Testing\Traits;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Testing\Traits\TestsFiles;
use Nonetallt\Helpers\Filesystem\Reflections\Psr4Reflection;

class TestsFilesTest extends TestCase
{
    use TestsFiles;

    public function testBasePath()
    {
        $ref = new Psr4Reflection($this);
        $this->assertEquals(dirname($ref->getNamespaceRoot()), $this->getBasePath());
    }

    public function testTestPathEqualsBasePathPlusTestDirName()
    {
        $ref = new Psr4Reflection($this);
        $this->assertEquals($ref->getNamespaceRoot(), $this->getTestPath());
    }

    public function testInputPathEqualsTestPathPlusInput()
    {
        $ref = new Psr4Reflection($this);
        $this->assertEquals($this->getTestPath('input'), $this->getTestInputPath());
    }

    public function testOutputPathEqualsTestPathPlusOutput()
    {
        $ref = new Psr4Reflection($this);
        $this->assertEquals($this->getTestPath('output'), $this->getTestOutputPath());
    }
}
