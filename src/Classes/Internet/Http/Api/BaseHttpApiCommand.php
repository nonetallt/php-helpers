<?php

namespace Nonetallt\Helpers\Internet\Http\Api;

use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;

abstract class BaseHttpApiCommand implements HttpApiCommand
{
    public static function getCommandName() : string
    {
        return lcfirst((new \ReflectionClass(static::class))->getShortName());
    }

    public function getResponse(HttpResponse $response)
    {
        return $response;
    }
}

