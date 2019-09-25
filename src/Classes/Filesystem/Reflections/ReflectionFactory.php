<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

use Nonetallt\Helpers\Filesystem\Reflections\Exceptions\AliasNotFoundException;
use CaseConverter\CaseConverter;
use Nonetallt\Helpers\Generic\Traits\ProxiesMethodCalls;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClassRepository;

/**
 * A class for constructing classes from reflections classes.
 *
 */
class ReflectionFactory extends ReflectionClassRepository
{
    use ProxiesMethodCalls;

    /* Can be overridden by child class */
    CONST CLASS_PREFIX = '';
    CONST CLASS_SUFFIX = '';

    public function __construct(string $class, ?string $dir = null, ?string $namespace = null)
    {
        parent::__construct($class, $dir, $namespace);
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Reflections\Exceptions\AliasNotFoundException
     */
    public function make(string $alias, ...$parameters)
    {
        $reflection = $this->items[$alias] ?? null;

        if($reflection === null) {
            $msg = $this->notFoundMessage($alias);
            throw new AliasNotFoundException($msg);
        }

        /* Return the reflection class if makeItem does not exist */
        if(! method_exists($this, 'makeItem')) {
            return $reflection;
        }

        array_unshift($parameters, $reflection);
        return $this->proxyForMethod('make', 'makeItem', $parameters);
    }

    /**
     * @override
     */
    protected function filterClasses(\ReflectionClass $class) : bool
    {
        return ! $class->isAbstract();
    }

    /**
     * override
     */
    protected function resolveAlias(\ReflectionClass $ref) : string
    {
        $alias = $ref->getShortName();

        /* Remove prefix */
        if(starts_with($alias, static::CLASS_PREFIX)) {
            $alias = substr($alias, strlen(static::CLASS_PREFIX));
        }

        /* Remove suffix */
        if(ends_with($alias, static::CLASS_SUFFIX)) {
            $len = strlen($alias) - strlen(static::CLASS_SUFFIX);
            $alias = substr($alias, 0, $len);
        }

        /* Change class name from studly case to snake */
        $case = new CaseConverter();
        $alias = $case->convert($alias)
            ->from('studly')
            ->to('snake');

        return $alias;
    }

    /**
     * Ment to be overridden by child class
     */
    protected function notFoundMessage(string $alias) : string
    {
        $class = self::class;
        return "Alias '$alias' could not be found in factory $class";
    }
}
