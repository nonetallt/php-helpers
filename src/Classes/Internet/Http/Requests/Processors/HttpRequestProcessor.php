<?php

namespace Nonetallt\Helpers\Internet\Http\Requests\Processors;

use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;

interface HttpRequestProcessor
{
    /**
     * Modify the request as neccesary
     *
     */
    public function processHttpRequest(HttpRequest $request) : HttpRequest;
}
