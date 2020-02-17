<?php

namespace Nonetallt\Helpers\Internet\Http\Api;

use Nonetallt\Helpers\Filesystem\Reflections\ReflectionFactory;

class HttpApiCommandFactory extends ReflectionFactory
{
    CONST COLLECTION_TYPE = HttpApiCommand::class;

    /**
     * override
     */
    protected function resolveAlias(\ReflectionClass $ref) : string
    {
        return ($ref->name)::getCommandName();
    }

    /**
     * @override
     *
     */
    protected function filterClass(\ReflectionClass $ref) : bool
    {
        return is_a($ref->name, HttpApiCommand::class, true);
    }
}
