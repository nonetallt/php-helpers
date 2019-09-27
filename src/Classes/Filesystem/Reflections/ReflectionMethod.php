<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

use ReflectionMethod as BaseReflectionMethod;

class ReflectionMethod extends BaseReflectionMethod
{
    public function getSignature() : string
    {
        return "{$this->class}::{$this->name}({$this->getParameterSignature()})";
    }

    public function getParameterSignature() : string
    {
        $parameters = [];

        foreach($this->getParameters() as $param) {
            $type = $param->getType()->getName();
            $parameters[] = "$type $$param->name";
        }

        return implode(', ', $parameters);
    }
}
