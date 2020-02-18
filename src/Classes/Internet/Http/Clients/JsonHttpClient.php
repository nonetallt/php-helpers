<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\JsonResponseParser;

class JsonHttpClient extends HttpClient
{
    protected function beforeRequest(HttpRequest $request) : HttpRequest
    {
        if(! is_a($request->getSettings()->response_parser, JsonResponseParser::class)) {
            $request->getSettings()->response_parser = new JsonResponseParser;
        }

        return $request;
    }
}
