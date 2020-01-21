<?php

namespace Nonetallt\Helpers\Filesystem\Traits;

trait FindsReflectionClasses
{
    use FindsFiles;

    protected function findReflectionClasses(string $dir, string $namespace, string $filterClass = null)
    {
        $classes = [];

        /* Append namespace separator if missing */
        if(! ends_with($namespace, '\\')) $namespace .= '\\';

        foreach($this->findFilesWithExtension($dir, '.php') as $file) {
            
            $class = basename($file, '.php');
            $model = "$namespace$class";
            $classes[] = $model;
        }

        if($filterClass !== null) {
            $classes = $this->filterInvalidClasses($classes, $filterClass);
        } 

        $reflections = [];
        foreach($classes as $class) {
            $reflections[] = $this->createReflectionClass($class);
        }

        return $reflections;
    }

    /**
     * Can be overridden by target class to customize created reflections
     */
    protected function createReflectionClass(string $class) : \ReflectionClass
    {
        return new \ReflectionClass($class);
    }

    private function filterInvalidClasses(array $classes, string $class)
    {
        return array_filter($classes, function($object) use ($class){
            return is_subclass_of($object, $class);
        });
    }
}
