<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\XmlResponseParser;

class XmlHttpClient extends HttpClient
{
    protected function beforeRequest(HttpRequest $request) : HttpRequest
    {
        if(! is_a($request->getSettings()->response_parser, XmlResponseParser::class)) {
            $request->getSettings()->response_parser = new XmlResponseParser;
        }

        return $request;
    }
}
