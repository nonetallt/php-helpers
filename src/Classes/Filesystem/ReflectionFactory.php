<?php

namespace Nonetallt\Helpers\Filesystem;
use Nonetallt\Helpers\Mapping\MethodMapping;
use Nonetallt\Helpers\Filesystem\Reflections\Exceptions\AliasNotFoundException;
use CaseConverter\CaseConverter;

class ReflectionFactory extends ReflectionRepository
{
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

        $mapping = new MethodMapping(new \ReflectionMethod($this, 'makeItem'));

        $parameters['reflection'] = $reflection;
        dd($mapping->validateMethodCall($parameters, new \ReflectionMethod($this, 'make')));

        return $this->makeItem($class, $parameters);
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
        $case = new CaseConverter();
        $alias = $case->convert($ref->getShortName())
            ->from('studly')
            ->to('snake');

        if(starts_with($alias, static::CLASS_PREFIX)) {
            $alias = substr($alias, strlen(static::CLASS_PREFIX));
        }

        if(starts_with($alias, static::CLASS_SUFFIX)) {
            $alias = substr($alias, strlen(static::CLASS_SUFFIX));
        }

        return $alias;
    }

    /**
     * Ment to be overridden by child class
     */
    protected function makeItem(\ReflectionClass $reflection)
    {
        return $reflection;
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
