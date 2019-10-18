<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Filesystem\Json\JsonParser;

class ParseJsonResponse extends ParseResponse implements HttpResponseProcessor
{
    /**
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */
    public function parseResponseBody(string $body) : array
    {
        $parser = new JsonParser();
        return $parser->decode($body, true);
    } 
}
