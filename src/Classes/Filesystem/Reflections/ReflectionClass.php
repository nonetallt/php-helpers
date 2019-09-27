<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

class ReflectionClass extends \ReflectionClass
{
    /**
     * Get directory root of the namespace of class
     *
     */
    public function getPsr4NamespaceRoot() : string
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

    /**
     * @override
     *
     * Get traits used by class
     *
     * @param bool $recursive Set to true if traits used by parent classes and
     * traits should be included.
     *
     * @param bool $autoload Wether classes should be autoloaded
     *
     * @return array $traits
     *
     */
    public function getTraits(bool $recursive = true, $autoload = true) : array
    {
        $class = $this->getName();

        /* Find traits of class */
        $traits = class_uses($class, $autoload);

        /* If no recusion is used, return base traits */
        if(! $recursive) return $traits;

        /* Find traits of all parent classes */
        while($class = get_parent_class($class)) {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        }

        /* Get traits of all parent traits */
        $traitsToSearch = $traits;
        while (! empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);

        };

        return array_unique($traits);
    }

    public function getConstructor()
    {
        $method = '__construct';

        if(! method_exists($this->getName(), $method)) {
            return null;
        }

        return new ReflectionMethod($this->getName(), $method);
    }
}
