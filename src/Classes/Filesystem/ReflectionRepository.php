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
    use FindsReflectionClasses, NamespaceBacktracerTrait;

    protected $callerClassReflection;
    protected $reflectionNamespace;
    protected $reflectionDir;

    public function __construct(string $class, ?string $dir = null, ?string $namespace = null)
    {
        $this->resolveCallerClassReflection();
        $this->setReflectionDir($dir);
        $this->setReflectionNamespace($namespace);

        parent::__construct($this->resolveReflections($class), \ReflectionClass::class);
    }

    protected function getIgnoredInterfaces() {
        return [self::class];
    }

    private function resolveCallerClassReflection()
    {
        $callerClass = $this->getCaller();
        $this->callerClassReflection = new \ReflectionClass($callerClass);
    }

    public function setReflectionDir(?string $dir)
    {
        /* Default null values to directory of the caller */
        if($dir === null) {
            $dir = dirname($this->callerClassReflection->getFileName());
        }

        if(! file_exists($dir)) throw new FileNotFoundException($dir); 
        if(! is_dir($dir))  throw new TargetNotDirectoryException($dir); 

        $this->reflectionDir = $dir;
    }

    public function setReflectionNamespace(?string $namespace)
    {
        /* Default null values to namespace of the caller */
        if($namespace === null) {
            $namespace = $this->callerClassReflection->getNamespaceName();
        }

        $this->reflectionNamespace = $namespace;
    }

    private function resolveReflections(string $class)
    {
        $refs = $this->findReflectionClasses($this->reflectionNamespace, $this->reflectionDir, $class);
        $mapped = [];

        foreach($refs as $ref) {
            $key = $this->resolveAlias($ref);
            $mapped[$key] = $ref;
        }

        return $mapped;
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
}
