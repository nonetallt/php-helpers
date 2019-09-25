<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use Nonetallt\Helpers\Describe\DescribeObject;
use BrightNucleus\NamespaceBacktracer\NamespaceBacktracerTrait;
use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotDirectoryException;

/**
 * A collection of reflection classes from a directory and namespace
 *
 * TODO: reload classes when dir or namespace changes
 *
 */
class ReflectionClassRepository extends Collection
{
    use FindsReflectionClasses;

    protected $reflectionClass;
    protected $reflectionNamespace;
    protected $reflectionDir;

    public function __construct(string $class, ?string $dir = null, ?string $namespace = null, string $collectionType = \ReflectionClass::class)
    {
        parent::__construct([], $collectionType);

        $this->setReflectionDir($dir);
        $this->setReflectionNamespace($namespace);
        $this->reflectionClass = $class;
        $this->loadReflections();
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException
     */
    public function setReflectionDir(?string $dir)
    {
        /* Default null values to directory of the subclass */
        if($dir === null) $dir = dirname((new \ReflectionClass($this))->getFileName()); 

        if(! file_exists($dir)) throw new FileNotFoundException($dir); 
        if(! is_dir($dir))  throw new TargetNotDirectoryException($dir); 

        $this->reflectionDir = $dir;
    }

    public function setReflectionNamespace(?string $namespace)
    {
        /* Default null values to namespace of the caller */
        if($namespace === null) $namespace = (new \ReflectionClass($this))->getNamespaceName(); 
        $this->reflectionNamespace = $namespace;
    }

    /**
     * Resets and loads all reflections from the currently set dir and
     * namespace
     *
     */
    public function loadReflections() : void
    {
        $this->items = [];
        $refs = $this->findReflectionClasses($this->reflectionNamespace, $this->reflectionDir, $this->reflectionClass);

        foreach($refs as $ref) {
            if(! $this->filterClass($ref)) continue;
            $key = $this->resolveAlias($ref);
            $this->items[$key] = $this->resolveClass($ref);
        }
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

    public function getReflectionNamespace() : string
    {
        return $this->reflectionNamespace;
    }

    public function getReflectionDir() : string
    {
        return $this->reflectionDir;
    }

    public function getReflectionClass() : string
    {
        return $this->reflectionClass;
    }

    public function getAliases() : array
    {
        return array_keys($this->items);
    }
}
