<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

class Psr4Reflection extends \ReflectionClass
{
    public function getNamespaceRoot() : string
    {
        $namespace = $this->getNamespaceName();
        $subfolderCount = substr_count($namespace, '\\');
        $filename = $this->getFileName();

        $directory = dirname($filename);

        for($n = 0; $n < $subfolderCount; $n++) {
            $directory = dirname($directory);
        }

        return $directory;
    }
}
