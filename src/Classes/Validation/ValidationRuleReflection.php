<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use CaseConverter\CaseConverter;

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
        /* Strip class name prefix from alias */
        $prefix = 'ValidationRule';
        if(starts_with($alias, $prefix)) $alias = substr($alias, strlen($prefix));

        $converter = new CaseConverter();
        $alias = $converter->convert($alias)->from('camel')->to('snake');

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
