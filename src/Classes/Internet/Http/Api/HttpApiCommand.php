<?php

namespace Nonetallt\Helpers\Internet\Http\Api;

use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;

interface HttpApiCommand
{
    /**
     * Get the name of the command, this command will become a method for the
     * api client
     *
     */
    public static function getCommandName() : string;

    /**
     * Get the http request that should be sent to the api
     *
     */
    public function getRequest() : HttpRequest;

    /**
     * Get the response that is returned after calling this command
     *
     */
    public function getResponse(HttpResponse $response);
}
