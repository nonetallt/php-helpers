<?php

namespace Test\Unit\Filesystem\Common;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Common\File;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotFileException;
use Nonetallt\Helpers\Filesystem\Exceptions\PermissionException;
use Nonetallt\Helpers\Testing\Traits\TestsFiles;

class FileTest extends TestCase
{
    use TestsFiles;

    private $file;

    public function setUp() : void
    {
        parent::setUp();
        $this->file = new File(__FILE__);
    }

    public function testExistsReturnsTrueWhenFileExists()
    {
        $this->assertTrue($this->file->exists());
    }

    public function testExistsReturnsFalseWhenFileDoesNotExist()
    {
        $this->file->setPath(__FILE__ . 'foobar');
        $this->assertFalse($this->file->exists());
    }

    public function testHasExtensionReturnsTrueWhenFileHasExtension()
    {
        $this->assertTrue($this->file->hasExtension());
    }

    public function testHasExtendsionReturnsFalseWhenFileHasNoExtension()
    {
        $this->file->setPath('foobar');
        $this->assertFalse($this->file->hasExtension());
    }

    public function testHasExtensionReturnsTrueWhenFileHasTheSpecifiedExtension()
    {
        $this->assertTrue($this->file->hasExtension('php'));
    }

    public function testHasExtensionReturnsFalseWhenFileDoesNotHaveTheSepcifiedExtension()
    {
        $this->assertFalse($this->file->hasExtension('json'));
    }

    public function testExtensionComparisonWorksWithLeadingDot()
    {
        $this->assertTrue($this->file->hasExtension('.php'));
    }

    public function testGetExtensionReturnsCorrectExtension()
    {
        $this->assertEquals('php', $this->file->getExtension());
    }

    public function testOpenStreamOpensResourceHandleWhenFileExists()
    {
        $this->assertTrue(is_resource($this->file->openStream('r')));
    }

    public function testOpenStreamThrowsFileNotFoundExceptionWhenFileDoesNotExist()
    {
        $this->expectException(FileNotFoundException::class);
        $this->file->setPath('foobar');
        $this->file->openStream('r');
    }

    public function testOpenStreamThrowsTargetNotFileExceptionWhenFileIsDir()
    {
        $this->expectException(TargetNotFileException::class);
        $this->file->setPath(__DIR__);
        $this->file->openStream('r');
    }

    public function testOpenStreamThrowsPermissionExceptionWhenReaderHasNoAccess()
    {
        $this->expectException(PermissionException::class);
        $this->file->setPath('/etc/sudoers');
        $this->file->openStream('r');
    }

    public function testIsFileReturnsTrueWhenPathPointsToFile()
    {
        $this->assertTrue($this->file->isFile());
    }

    public function testIsFileReturnsFalseWhenPathPointsToDir()
    {
        $this->file->setPath(__DIR__);
        $this->assertFalse($this->file->isFile());
    }

    public function testIsFileReturnsFalseWhenPathDoesNotExist()
    {
        $this->file->setPath(__DIR__ . 'foobar.json');
        $this->assertFalse($this->file->isFile());
    }

    public function testIsDirReturnsTrueWhenPathPointsToDir()
    {
        $this->file->setPath(__DIR__);
        $this->assertTrue($this->file->isDir());
    }

    public function testIsDirReturnsFalseWhenPathPointsToFile()
    {
        $this->assertFalse($this->file->isDir());
    }

    public function testIsDirReturnsFalseWhenPathDoesNotExist()
    {
        $this->file->setPath(__DIR__ . 'foobar.json');
        $this->assertFalse($this->file->isDir());
    }

    public function testGetSizeReturnsInteger()
    {
        $this->assertTrue(is_integer($this->file->getSize()));
    }

    public function testGetSizeThrowsFileNotFoundExceptionWhenFileDoesNotExist()
    {
        $this->file->setPath(__DIR__ . 'foobar.json');
        $this->expectException(FileNotFoundException::class);
        $this->file->getSize();
    }
}
