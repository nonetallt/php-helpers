<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;

class ValidationRuleReflection
{
    private $name;
    private $namespace;
    private $alias;

    public function __construct(\ReflectionClass $reflection)
    {
        $this->fullName = $reflection->getName();
        $this->setAlias($reflection->getShortName());
    }

    public function __toString()
    {
        return $this->alias;
    }

    public function setAlias(string $alias)
    {
        /* TODO convert from snake to camel case */
        $alias = strtolower($alias);

        /* Strip class name prefix from alias */
        $prefix = 'validationrule';
        if(starts_with($alias, $prefix)) $alias = substr($alias, strlen($prefix));

        $this->alias = $alias;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function getAlias()
    {
        return $this->alias;
    }
}
