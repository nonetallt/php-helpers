<?php

namespace Nonetallt\Helpers\Internet\Http;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

/**
 * A http client that parses xml responses
 */
class XmlHttpClient extends ResponseParsingHttpClient
{
    protected function createResponseClass(HttpRequest $request, ?Response $response, ?RequestException $exception = null) : ParsedHttpResponse
    {
        return new XmlHttpResponse($request, $response);
    }
}
