<?php

namespace Nonetallt\Helpers\Filesystem\Common;

use Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotFileException;
use Nonetallt\Helpers\Filesystem\Permissions\FilePermissions;
use Nonetallt\Helpers\Generic\Traits\LazyLoadsProperties;
use Nonetallt\Helpers\Strings\Str;
use Nonetallt\Helpers\Strings\Languages\English;

class File implements \IteratorAggregate
{
    use LazyLoadsProperties;

    private $path;

    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    /**
     * Create a new temporary file
     *
     */
    public static function temp() : self
    {
        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);

        return new self($meta['uri']);
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

    public function dirname() : string
    {
        return dirname($this->getPath());
    }

    public function getContent() : string
    {
        return file_get_contents($this->getPath());
    }   

    public function getIterator() : \Traversable
    {
        return new FileLineIterator($this);
    }

    public function getFile() : File
    {
        return $this->file;
    }

    /**
     * string|FileLineIterator|File $content
     *
     *
     */
    public function write($content)
    {
        if(is_string($content)) {
            file_put_contents($this->getPath(), $content);
            return;
        }

        if(is_a($content, File::class)) {
            $this->writeLines($content->getLines());
            return;
        }

        if(is_a($content, FileLineInterator::class)) {
            $this->writeLines($content);
            return;
        }
        dd(is_a($content, FileLineInterator::class));

        $types = ['string', File::class, FileLineInterator::class];
        $msg = 'Content must be ' . English::listWords($types, 'or');
        throw new \InvalidArgumentException($msg);
    }

    public function writeLines(FileLineIterator $lines)
    {
        $stream = $this->openStream('w');
        foreach($lines as $line) {
            fwrite($stream, $line->getContent());
        }
        fclose($stream);
    }

    public function copy(string $destination) : self
    {
        $stream = fopen($destination, 'w');

        foreach($this->getLines() as $line) {
            fwrite($stream, $line->getContent());
        }

        fclose($stream);

        return new self($destination);
    }

    public function move(string $destination)
    {
        $this->copy($destination);
        unlink($this->getPath());
        $this->setPath($destination);
    }

    public function rename(string $name)
    {
        $this->move($this->dirname() . DIRECTORY_SEPARATOR . $name);
    }
}
