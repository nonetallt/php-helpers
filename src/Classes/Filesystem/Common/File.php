<?php

namespace Nonetallt\Helpers\Filesystem\Common;

use Nonetallt\Helpers\Generic\Container;
use Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotFileException;
use Nonetallt\Helpers\Filesystem\Permissions\FilePermissions;

class File
{
    private $path;
    private $cache;

    public function __construct(string $path)
    {
        $this->cache = new Container();
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
        while(starts_with($extension, '.')) {
            $extension = substr($extension, 1);
        }

        /* Check if the file extension equals the one given by user */
        return $this->getExtension() === $extension;
    }
    

    public function setPath(string $path)
    {
        /* Reset cache if path has changed */
        if($path !== $this->path) $this->cache->reset();
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
    public function getSize() : int
    {
        if(! $this->cache->has('size')) {
            if(! $this->exists()) throw new FileNotFoundException($this->path);
            $this->cache->size = filesize($this->path);
        }
        return $this->cache->size;
    }

    public function getLines() : FileLineIterator
    {
        return new FileLineIterator($this);
    }

    public function getPermissions() : FilePermissions
    {
        return new FilePermissions($this->path);
    }

    public function getExtension() : string
    {
        if(! $this->cache->has('extension')) {
            $parts = explode('.', $this->path);
            $partsCount = count($parts);

            /* File does not have an extension */
            if($partsCount < 2) return '';

            $this->cache->extension = $parts[$partsCount - 1];
        }
        
        return $this->cache->extension;
    }

    public function getPath() : string
    {
        return $this->path;
    }
}
