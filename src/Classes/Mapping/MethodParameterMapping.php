<?php

namespace Nonetallt\Helpers\Mapping;


use Nonetallt\Helpers\Mapping\Exceptions\MappingException;
use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Mapping\Exceptions\ParameterValueMappingException;

class MethodParameterMapping extends ParameterMappingCollection
{
    private $reflection;

    /**
     * @throws Nonetallt\Helpers\Mapping\Exceptions\MappingException
     */
    public function __construct(\ReflectionMethod $method)
    {
        $this->reflection = $method;
        $parameters = [];

        foreach($method->getParameters() as $parameter) {
            $parameters[] = new MethodParameter($parameter);
        }

        parent::__construct($parameters);
    }

    /**
     * @throws ArgumentCountError
     * @throws TypeError
     *
     */
    public function mapMethod(array $parameters = [], int $traceDepth = 0, ?\ReflectionMethod $proxy = null) : array
    {
        $reflection = $proxy ?? $this->reflection;
        
        $class = $reflection->getDeclaringClass()->name;
        $expected = $this->reflection->getNumberOfRequiredParameters();
        $method = $reflection->getName();
        $passed = count($parameters);

        /* Argument count is checked first by php parser */
        if($passed < $expected) {
            $trace = debug_backtrace()[$traceDepth];
            $file = $trace['file'];
            $line = $trace['line'];
            $msg = "Too few arguments to function {$class}::{$method}(), $passed passed in $file on line $line and at least $expected expected";
            throw new \ArgumentCountError($msg);
        }

        try {
            return $this->mapArray($parameters);
        }
        catch(ParameterValueMappingException $e) {
            $trace = debug_backtrace()[$traceDepth];
            $file = $trace['file'];
            $line = $trace['line'];
            $mapping = $e->getMapping();
            $position = $mapping->getPosition() + 1;
            $type = $mapping->getType();
            $given = (new DescribeObject($e->getValue()))->describeType(true);

            $msg = "Argument $position passed to {$class}::{$method}(), must be of the type $type, $given given, called in $file on line $line";
            throw new \TypeError($msg, 0, $e);
        }
    }
}
