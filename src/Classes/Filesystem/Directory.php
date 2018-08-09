<?php

namespace Nonetallt\Helpers\Filesystem;

class Directory
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function create(int $mode, bool $recursive = false)
    {
        return mkdir($this->path, $mode, $recursive);
    }

    public function isValid()
    {
        return is_dir($this->path);
    }

    public function removeRecursive(int $level = 1)
    {
        if(! $this->isValid()) return;
        
        /* Get contents of this folder, excluding self/parent paths */
        $objects = array_diff(scandir($this->path), ['.', '..']); 
        echo $objects;
        exit();

        foreach ($objects as $object) { 

            /* Recursively remove contents of folders */
            if (is_dir($this->path."/".$object)) {
                $dir = new self($this->path."/".$object);
                $dir->removeRecursive($level + 1);
                continue;
            }

            /* Remove files */
            unlink($this->path."/".$object); 
        }

        if($level > 1) rmdir($this->path); 
    }
}
