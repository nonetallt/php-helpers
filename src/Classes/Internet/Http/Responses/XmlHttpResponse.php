<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use Nonetallt\Helpers\Filesystem\Xml\XmlParser;

class XmlHttpResponse extends ParsedHttpResponse
{
    /**
     * @throws Nonetallt\Helpers\Filesystem\Xml\Exceptions\XmlParsingException
     */
    protected function parseBody(string $body)
    {
        $parser = new XmlParser();
        return $parser->decode($body, true);
    }
}
