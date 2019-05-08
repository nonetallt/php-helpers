<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use GuzzleHttp\Psr7\Response;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\ParsedHttpResponse;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Responses\XmlHttpResponse;

/**
 * A http client that parses xml responses
 */
class XmlHttpClient extends ResponseParsingHttpClient
{
    protected function createResponseClass(HttpRequest $request, ?Response $response, HttpRequestExceptionCollection $exceptions) : ParsedHttpResponse
    {
        return new XmlHttpResponse($request, $response, $exceptions);
    }
}
