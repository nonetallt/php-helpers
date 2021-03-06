<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

use Nonetallt\Helpers\Filesystem\Reflections\Exceptions\AliasNotFoundException;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClassRepository;
use Jawira\CaseConverter\Convert;
use Nonetallt\Helpers\Generic\Traits\ProxiesMethodCalls;

/**
 * A class for constructing classes from reflections classes.
 *
 */
class ReflectionFactory extends ReflectionClassRepository
{
    use ProxiesMethodCalls;

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

        if(method_exists($this, 'makeItem')) {
            array_unshift($parameters, $reflection);
            return $this->proxyForMethod('make', $this, 'makeItem', $parameters);
        }

        $class = $reflection->name;
        return new $class(...$parameters);
    }

    /**
     * @override
     */
    protected function filterClass(\ReflectionClass $ref) : bool
    {
        return parent::filterClass($ref) && ! $ref->isAbstract();
    }
}
