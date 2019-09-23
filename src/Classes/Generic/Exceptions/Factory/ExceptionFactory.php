<?php

namespace Nonetallt\Helpers\Generic\Exceptions\Factory;

use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection;
use Nonetallt\Helpers\Generic\Exceptions\NotFoundException;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionMethodFactory;

class ExceptionFactory
{
    private $handlers;

    public function __construct()
    {
        $prefix = 'handle';
        $suffix = 'Exception';
        $this->handlers = new ReflectionMethodFactory($this, $prefix, $suffix);
    }

    protected function createExceptionCollection() : ExceptionCollection
    {
        return new ExceptionCollection();
    }

    /**
     * Use exception data to find a key that will be used for finding a correct
     * exception handler for this kind of data.
     *
     * @param mixed $exceptionData The exception data that should be resolved
     *
     * @return string $key The key that will be used to find the correct
     * handler
     *
     */
    protected function resolveHandlerKey($exceptionData) : string
    {
        $desc = new DescribeObject($exceptionData);
        $type = $desc->describeType();
        return $type;
    }

    /**
     * Create exceptions using handlers methods defined by this class
     *
     * @throws Nonetallt\Helpers\Generic\Exceptions\NotFoundException
     *
     * @param mixed $exceptionData Exception data that should be used for
     * creating exceptions
     *
     * @return Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection
     * $exceptions
     *
     */
    public function createExceptions($exceptionData) : ExceptionCollection
    {
        $key = $this->resolveHandlerKey($exceptionData);

        if(! isset($this->handlers[$key])) {
            $msg = "No exception handler for key '$key'";
            throw new NotFoundException($msg);
        }

        /* Find the relevant handler method */
        $handlerMethodReflection = $this->handlers[$key];
        $handlerMethodName = $handlerMethodReflection->getName();

        /* Call the handler method */
        $exceptions = $this->createExceptionCollection();
        $this->$handlerMethodName($exceptionData, $exceptions);

        return $exceptions;
    }
}
