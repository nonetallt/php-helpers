<?php

namespace Nonetallt\Helpers\Internet\Http\Api;

use Nonetallt\Helpers\Filesystem\Reflections\ReflectionFactory;

class HttpApiCommandFactory extends ReflectionFactory
{
    /**
     * override
     */
    protected function resolveAlias(\ReflectionClass $ref) : string
    {
        return ($ref->name)::getCommandName();
    }
}
