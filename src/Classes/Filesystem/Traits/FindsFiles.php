<?php

namespace Nonetallt\Helpers\Filesystem\Traits;

trait FindsFiles
{
    protected function findFiles(string $dir)
    {
        if(! is_dir($dir)) return [];
        return array_diff(scandir($dir), ['.', '..']);
    }

    protected function findFilesWithExtension(string $dir, string $extension)
    {
        /* Prepend missing dot */
        if(! starts_with($extension, '.')) $extension = ".$extension";

        return array_filter($this->findFiles($dir), function($file) use($extension){
            return ends_with($file, $extension);
        });
    }

}
