<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Filesystem\Json\JsonParser;

class JsonResponseParser implements ResponseParser
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
