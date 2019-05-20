<?php

namespace Nonetallt\Helpers\Generic\Traits;

use Nonetallt\Helpers\Mapping\MethodParameterMapping;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;

trait ProxiesMethodCalls
{
    /**
     * Cache reflection methods to avoid having to load them again for multiple
     * method calls to the same method
     */
    private $proxies = [];

    private function initializeProxy(string $name)
    {
        if(! method_exists($this, $name)) {
            $class = get_class($this);
            $msg = "Cannot proxy for non-existent method {$class}::{$name}()";
            throw new \InvalidArgumentException($msg);
        }

        $this->proxies[$name] = new \ReflectionMethod($this, $name);
    }

    private function getProxy(string $methodName) : \ReflectionMethod
    {
        if(! isset($this->proxies[$methodName])) {
            $this->initializeProxy($methodName);
        }

        return $this->proxies[$methodName];
    }

    /**
     * Handle method call as if it the proxy was called instead of the actually
     * called method
     */
    private function proxyForMethod(string $proxy, string $method, array $parameters)
    {
        $callerMethod = $this->getProxy($proxy);
        $calledMethod = $this->getProxy($method);

        $mapping = new MethodParameterMapping($calledMethod);

        /* Depth is 0) this -> 1) trait implementer calling this method -> 2) caller */
        $mappedParameters = $mapping->mapMethod($parameters, 2, $callerMethod);
        $methodName = $calledMethod->name;

        return $this->$methodName(...$mappedParameters);
    }
}
