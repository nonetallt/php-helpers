<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestResponseException;
use Nonetallt\Helpers\Generic\Exceptions\ParsingException;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponseHandler;

/**
 * Processor that creates exceptions for response data that can't
 * be parsed
 *
 */
class CreateConnectionExceptions implements HttpResponseProcessor
{
    public function process(HttpResponse $response, HttpResponseHandler $handler)  : HttpResponse
    {
        $response->getExceptions()->pushAll($handler->getConnectionExceptions($response->getRequest()));
        return $response;
    }
}
