<?php

namespace Test\Mock\ReflectionFactory;

use Nonetallt\Helpers\Filesystem\Reflections\ReflectionFactory;
use Nonetallt\Helpers\Strings\Str;

class ExceptionFactory extends ReflectionFactory
{
    public function __construct()
    {
        parent::__construct();
        $this->loadReflections();
    }

    /**
     * Create a class in response to factory make
     *
     */
    protected function makeItem(\ReflectionClass $reflection, string $message, int $code = 0, \Exception $previous = null)
    {
        $class = $reflection->name;
        return new $class($message, $code, $previous);
    }

    /**
     * @override
     *
     */
    protected function resolveAlias(\ReflectionClass $ref) : string
    {
        return Str::removeSuffix($ref->getShortName(), 'Exception');
    }

    /**
     * @override
     *
     */
    protected function filterClass(\ReflectionClass $ref) : bool
    {
        return is_a($ref->getName(), \Exception::class, true);
    }
}
