<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

use Nonetallt\Helpers\Generic\Collection;

/**
 *  A class 
 */
class ReflectionMethodRepository extends Collection
{
    public function __construct($object)
    {
        parent::__construct([], \ReflectionMethod::class);
        $this->loadReflectionMethods($object);
    }

    public function loadReflectionMethods(Object $object)
    {
        if(! is_a($object, \ReflectionClass::class)) {
            $object = new \ReflectionClass($object);
        }

        foreach($object->getMethods() as $ref) {
            if(! $this->filterMethod($ref)) continue;
            $key = $this->resolveAlias($ref);
            $this->items[$key] = $ref;
        }
    }

    /**
     * Filter methods when they are loaded from the reflectionClass.
     *
     * Ment to be override by child class
     *
     * @param \ReflectionMethod The method to filter
     *
     * @return bool $shouldInclude True if this method should be included,
     * false otherwise
     */
    protected function filterMethod(\ReflectionMethod $method) : bool
    {
        return true;
    }

    /**
     * 
     * Get the key that should be used to access this method.
     *
     * Ment to be overridden by child class
     *
     * @param \ReflectionMethod $method
     *
     * @return string $key
     *
     */
    protected function resolveAlias(\ReflectionMethod $method) : string
    {
        return $method->getName();
    }
}
