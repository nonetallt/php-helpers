<?php

namespace Test\Mock\ReflectionFactory;

use Nonetallt\Helpers\Filesystem\Reflections\ReflectionFactory;

class ExceptionFactory extends ReflectionFactory
{
    CONST CLASS_SUFFIX = 'Exception';

    public function __construct()
    {
        parent::__construct(\Exception::class);
    }

    protected function makeItem(\ReflectionClass $reflection, string $message, int $code = 0, \Exception $previous = null) : \Exception
    {
        $class = $reflection->name;
        return new $class($message, $code, $previous);
    }
}
