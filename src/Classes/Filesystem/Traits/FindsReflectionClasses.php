<?php

namespace Nonetallt\Helpers\Filesystem\Traits;

trait FindsReflectionClasses
{
    use FindsFiles;

    protected function findReflectionClasses(string $namespace, string $dir, string $filterClass = null)
    {
        $classes = [];

        /* Append namespace separator if missing */
        if(! ends_with($namespace, '\\')) $namespace .= '\\';

        foreach($this->findFilesWithExtension($dir, '.php') as $file) {
            
            $class = basename($file, '.php');
            $model = "$namespace$class";
            $classes[] = $model;
        }

        /* If class option is supplied, filter non-subclass classes */
        if(! is_null($filterClass)) $classes = $this->filterInvalidClasses($classes, $filterClass);

        $reflections = [];
        foreach($classes as $class) {
            $reflections[] = new \ReflectionClass($class);
        }

        return $reflections;
    }

    private function filterInvalidClasses(array $classes, string $class)
    {
        return array_filter($classes, function($object) use ($class){
            return is_subclass_of($object, $class);
        });
    }
}
