<?php

namespace Nonetallt\Helpers\Generic\Traits;

/**
 * Trait for lazy loading properties.
 *
 * The user class creates a method called "lazyLoadX" where X is the name of
 * the property that should be lazy loaded and this trait adds a proxy getter
 * using the magic __call method for that property that only loads the value
 * once.
 *
 */
trait LazyLoadsProperties
{
    private $lazyLoadedProperties = [];

    /**
     * Get the method prefix that is used to identify lazy loading methods
     *
     */
    public function getLazyLoadMethodPrefix() : string
    {
        return "lazyLoad";
    }

    /**
     * Get all the properties that have already been loaded using lazy loading
     *
     */
    public function getLazyLoadedProperties() : array
    {
        return $this->lazyLoadedProperties;
    }

    /**
     * Attempt to lazy load the value for a given property
     * 
     */
    public function lazyLoadProperty(string $name)
    {
        $prefix = $this->getLazyLoadMethodPrefix();
        $methodName = $prefix . ucfirst($name);
        $propName = lcfirst($name);

        if(! method_exists($this, $methodName)) {
            $msg = "No method declared for lazy loading property '$propName'";
            throw new \InvalidArgumentException($msg);
        }


        if(! array_key_exists($propName, $this->lazyLoadedProperties)) {
            $value = $this->$methodName();
            $this->lazyLoadedProperties[$propName] = $value;
        }

        return $this->lazyLoadedProperties[$propName];
    }

    /**
     * Forget a value of a loaded property so that it will be loaded again when
     * required next time
     *
     */
    public function forgetLazyLoadedProperty(string $name)
    {
        unset($this->lazyLoadedProperties[$name]);
    }

    /**
     * Forget values of all loaded properties so that they will be loaded again
     * when required next time
     *
     */
    public function forgetLazyLoadedProperties()
    {
        $this->lazyLoadedProperties = [];
    }

    /**
     * Proxy method calls starting with "get" to lazy loader methods defined by
     * user class
     *
     */
    public function __call(string $method, $parameters)
    {
        if(starts_with($method, 'get')) {
            $prop = substr($method, strlen('get'));
            return $this->lazyLoadProperty($prop);
        }

        trigger_error('Call to undefined method '.__CLASS__.'::'.$method.'()', E_USER_ERROR);
    }
}
