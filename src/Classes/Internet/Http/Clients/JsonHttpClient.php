<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use GuzzleHttp\Psr7\Response;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\ParsedHttpResponse;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Responses\JsonHttpResponse;

/**
 * A http client that parses json responses
 */
class JsonHttpClient extends ResponseParsingHttpClient
{
    protected function createResponseClass(HttpRequest $request, ?Response $response, HttpRequestExceptionCollection $exceptions) : ParsedHttpResponse
    {
        return new JsonHttpResponse($request, $response, $exceptions);
    }
}
