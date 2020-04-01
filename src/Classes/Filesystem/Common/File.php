<?php

namespace Nonetallt\Helpers\Filesystem\Common;

use Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotFileException;
use Nonetallt\Helpers\Filesystem\Permissions\FilePermissions;
use Nonetallt\Helpers\Generic\Traits\LazyLoadsProperties;
use Nonetallt\Helpers\Strings\Str;

class File implements \IteratorAggregate
{
    use LazyLoadsProperties;

    private $path;

    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    public function exists() : bool
    {
        return file_exists($this->path);
    }

    public function hasExtension(string $extension = null) : bool
    {
        $ext = $this->getExtension();

        /* File has extension if it is not empty */
        if($extension === null) return $ext !== '';

        /* Remove leading dots for comparison */
        while(Str::startsWith($extension, '.')) {
            $extension = substr($extension, 1);
        }

        /* Check if the file extension equals the one given by user */
        return $this->getExtension() === $extension;
    }
    

    public function setPath(string $path)
    {
        /* Reset cache if path has changed */
        if($path !== $this->path) $this->forgetLazyLoadedProperties();
        $this->path = $path;
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException
     *
     * @param string $mode fopen() mode
     * @return resource $stream
     *
     */
    public function openStream(string $mode)
    {
        if(! $this->exists()) {
            throw new FileNotFoundException($this->path);
        }

        if($this->isDir()) {
            throw new TargetNotFileException($this->path);
        }

        $this->getPermissions()->validateStreamMode($mode);

        $stream = fopen($this->path, $mode);

        if($stream === false) {
            $msg = 'Could not open stream';
            throw new FilesystemException($msg, $this->path);
        }

        return $stream;
    }

    public function isDir() : bool
    {
        return is_dir($this->path);
    }

    public function isFile() : bool
    {
        return is_file($this->path);
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException
     *
     * @return int $size filesize in bytes
     *
     */
    public function lazyLoadSize() : int
    {
        if(! $this->exists()) {
            throw new FileNotFoundException($this->path);
        }

        return filesize($this->path);
    }

    public function getLines() : FileLineIterator
    {
        return new FileLineIterator($this);
    }

    public function getPermissions() : FilePermissions
    {
        return new FilePermissions($this->path);
    }

    public function lazyLoadExtension() : string
    {
        $parts = explode('.', $this->path);
        $partsCount = count($parts);

        /* File does not have an extension */
        if($partsCount < 2) return '';

        return $parts[$partsCount - 1];
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function getIterator() : \Traversable
    {
        return new FileLineIterator($this);
    }
}
