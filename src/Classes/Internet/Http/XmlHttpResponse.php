<?php

namespace Nonetallt\Helpers\Internet\Http;

use Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException;
use Nonetallt\Helpers\Filesystem\Json\JsonParser;

class XmlHttpResponse extends ParsedHttpResponse
{
    /**
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */
    protected function parseBody(string $body) : array
    {
        throw new \Exception('TODO');

        $parser = new JsonParser();
        return $parser->decode($body, true);
    }
}
