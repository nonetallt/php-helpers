<?php

namespace Nonetallt\Helpers\Filesystem;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use Nonetallt\Helpers\Describe\DescribeObject;
use BrightNucleus\NamespaceBacktracer\NamespaceBacktracerTrait;
use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotDirectoryException;

/**
 * A class for constructing classes from reflections.
 *
 */
class ReflectionRepository extends Collection
{
    use FindsReflectionClasses;

    protected $reflectionClass;
    protected $reflectionNamespace;
    protected $reflectionDir;

    public function __construct(string $class, ?string $dir = null, ?string $namespace = null)
    {
        $this->setReflectionDir($dir);
        $this->setReflectionNamespace($namespace);
        $this->reflectionClass = $class;

        parent::__construct($this->resolveReflections(), \ReflectionClass::class);
    }

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

    private function resolveReflections()
    {
        $refs = $this->findReflectionClasses($this->reflectionNamespace, $this->reflectionDir, $this->reflectionClass);
        $mapped = [];

        foreach($refs as $ref) {
            if(! $this->filterClasses($ref)) continue;
            $key = $this->resolveAlias($ref);
            $mapped[$key] = $ref;
        }

        return $mapped;
    }

    /**
     * Wether class should be used. Ment to be overriden by child classes
     */
    protected function filterClasses(\ReflectionClass $ref) : bool
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
        return $ref->getShortName();
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
}
