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

    private function initializeProxy(string $class, string $method)
    {
        if(! method_exists($this, $method)) {
            $msg = "Cannot proxy for non-existent method {$class}::{$method}()";
            throw new \InvalidArgumentException($msg);
        }

        $this->proxies[$class][$method] = new \ReflectionMethod($class, $method);
    }

    private function getProxy(string $class, string $method) : \ReflectionMethod
    {
        if(! isset($this->proxies[$class][$method])) {
            $this->initializeProxy($class, $method);
        }

        return $this->proxies[$class][$method];
    }

    /**
     * Handle method call as if it the proxy was called instead of the actually
     * called method
     */
    private function proxyForMethod(string $proxy, string $class, string $method, array $parameters)
    {
        $callerMethod = $this->getProxy(static::class, $proxy);
        $calledMethod = $this->getProxy($class, $method);

        $mapping = new MethodParameterMapping($calledMethod);

        /* Depth is 0) this -> 1) trait implementer calling this method -> 2) caller */
        $mappedParameters = $mapping->mapMethod($parameters, 2, $callerMethod);
        $methodName = $calledMethod->name;

        if($method === '__construct') {
            return new $class(...$mappedParameters);
        }

        return $class->$methodName(...$mappedParameters);
    }
}
