<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use Nonetallt\Helpers\Describe\DescribeObject;
use BrightNucleus\NamespaceBacktracer\NamespaceBacktracerTrait;
use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotDirectoryException;

/**
 * A loadable collection of reflection classes
 *
 */
class ReflectionClassRepository extends Collection
{
    use FindsReflectionClasses;

    CONST COLLECTION_TYPE = \ReflectionClass::class;

    /**
     * Resets and loads all reflections from the currently set dir and
     * namespace
     *
     */
    public function loadReflections(string $dir = null, string $namespace = null) : void
    {
        $dir = $dir ?? $this->getDefaultReflectionDir();
        $this->validateReflectionDir($dir);

        $namespace = $namespace ?? $this->getDefaultReflectionNamespace();

        $this->items = [];
        $refs = $this->findReflectionClasses($dir, $namespace);

        foreach($refs as $ref) {
            if(! $this->filterClass($ref)) continue;
            $key = $this->resolveAlias($ref);
            $this->items[$key] = $this->resolveClass($ref);
        }
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException
     */
    private function validateReflectionDir(?string $dir)
    {
        if(! file_exists($dir)) throw new FileNotFoundException($dir); 
        if(! is_dir($dir))  throw new TargetNotDirectoryException($dir); 
    }

    /**
     * Get the directory that should be used for loading when one is not
     * provided to loadReflections()
     * 
     */
    public function getDefaultReflectionDir() : string
    {
        return dirname((new \ReflectionClass($this))->getFileName()); 
    }

    /**
     * Get the namespace that should be used for loading when one is not
     * provided to loadReflections()
     *
     */
    public function getDefaultReflectionNamespace() : string
    {
        /* Default null values to namespace of the caller */
        return (new \ReflectionClass($this))->getNamespaceName(); 
    }

    /**
     * Wether class should be used. Ment to be overriden by child classes
     *
     */
    protected function filterClass(\ReflectionClass $ref) : bool
    {
        return true;
    }

    /**
     *  Get the string 'alias' used to access this reflection in the repository.
     *  This method should be overridden by child classes to customize alias
     *  naming preferences.
     *
     */
    protected function resolveAlias(\ReflectionClass $ref) : string
    {
        return $ref->name;
    }

    /**
     *  Load reflection class to this collection. Can be customized by child to
     *  create other classes from the reflection class data.
     *
     */
    protected function resolveClass(\ReflectionClass $ref)
    {
        return $ref;
    }

    public function getAliases() : array
    {
        return array_keys($this->items);
    }
}
