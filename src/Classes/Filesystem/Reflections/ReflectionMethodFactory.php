<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

/**
 * A class for accessing methods of a certain class using simplified key to
 * method mapping defined by user
 */
class ReflectionMethodFactory extends ReflectionMethodRepository
{
    private $object;

    private $handlerMethodPrefix;
    private $handlerMethodSuffix;

    public function __construct(Object $object, string $handlerMethodPrefix, string $handlerMethodSuffix)
    {
        $this->setHandlerMethodPrefix($handlerMethodPrefix);
        $this->setHandlerMethodSuffix($handlerMethodSuffix);
        parent::__construct($object);
    }

    public function setHandlerMethodPrefix(string $handlerMethodPrefix)
    {
        $this->handlerMethodPrefix = $handlerMethodPrefix;
    }

    public function setHandlerMethodSuffix(string $handlerMethodSuffix)
    {
        $this->handlerMethodSuffix = $handlerMethodSuffix;
    }

    /**
     * @override
     *
     * Filter methods that can't be accessed by the class or do not match the
     * prefix - suffix pattern
     *
     */
    protected function filterMethod(\ReflectionMethod $method) : bool
    {
        /* Skip inaccessible methods */
        if(! $method->isPublic() && ! $method->isProtected()) {
            return false;
        }

        /* Skip methods that are missing either prefix or suffix */
        if(! starts_with($method->getName(), $this->handlerMethodPrefix) || 
            ! ends_with($method->getName(), $this->handlerMethodSuffix)) {
            return false;
        }

        return true;
    }

    /**
     * @override
     *
     * Resolve alias of each method based on the first argument type
     * Example: function resolveString(string $str) would have a key of 'string'
     *
     */
    protected function resolveAlias(\ReflectionMethod $method) : string
    {
        $type = $method->getParameters()[0]->getType();

        if($type instanceof \ReflectionNamedType) {
            return $type->getName();
        }

        $msg = "Unnamed reflection type";
        throw new \Exception($msg);
    }
}
