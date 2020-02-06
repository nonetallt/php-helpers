<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use Jawira\CaseConverter\Convert;

class ValidationRuleReflection extends \ReflectionClass
{
    CONST CLASS_PREFIX = 'ValidationRule';

    private $alias;

    public function __construct($class)
    {
        parent::__construct($class);
    }

    public function __toString()
    {
        return $this->alias;
    }

    private function resolveAlias() : string
    {
        $alias = $this->getShortName();

        /* Strip class name prefix from alias */
        if(starts_with($alias, self::CLASS_PREFIX)) {
            $alias = substr($alias, strlen(self::CLASS_PREFIX));
        }

        $converter = new Convert($alias);
        return $converter->fromCamel()->toSnake();
    }

    public function getAlias() : string
    {
        if($this->alias === null) {
            $this->alias = $this->resolveAlias();
        }
        return $this->alias;
    }
}
