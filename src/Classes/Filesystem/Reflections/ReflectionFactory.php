<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

use Nonetallt\Helpers\Filesystem\Reflections\Exceptions\AliasNotFoundException;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClassRepository;

/**
 * A class for constructing classes from reflections classes.
 *
 */
class ReflectionFactory extends ReflectionClassRepository
{
    /**
     * @throws Nonetallt\Helpers\Filesystem\Reflections\Exceptions\AliasNotFoundException
     */
    public function make(string $alias, ...$parameters)
    {
        $reflection = $this->items[$alias] ?? null;

        if($reflection === null) {
            $class = static::class;
            $msg = "Alias '$alias' could not be found in factory $class";
            throw new AliasNotFoundException($msg);
        }

        return $this->makeItem($reflection, ...$parameters);
    }

    /**
     * Create a class in response to factory make
     *
     */
    protected function makeItem(\ReflectionClass $reflection, array $parameters)
    {
        $class = $reflection->name;
        return new $class(...$parameters);
    }

    /**
     * @override
     */
    protected function filterClasses(\ReflectionClass $class) : bool
    {
        return ! $class->isAbstract();
    }
}
