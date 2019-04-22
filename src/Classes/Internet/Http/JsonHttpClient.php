<?php

namespace Nonetallt\Helpers\Internet\Http;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

/**
 * A http client that parses json responses
 */
class JsonHttpClient extends ResponseParsingHttpClient
{
    protected function createResponseClass(HttpRequest $request, ?Response $response, ?RequestException $exception = null) : ParsedHttpResponse
    {
        return new JsonHttpResponse($request, $response);
    }
}
