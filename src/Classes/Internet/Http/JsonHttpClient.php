<?php

namespace Nonetallt\Helpers\Internet\Http;

use GuzzleHttp\Psr7\Response;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;

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
