<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Filesystem\Xml\XmlParser;

class ParseXmlResponse extends ParseResponse implements HttpResponseProcessor
{
    /**
     * @throws Nonetallt\Helpers\Filesystem\Xml\Exceptions\XmlParsingException
     */
    public function parseResponseBody(string $body) : array
    {
        $parser = new XmlParser();
        return $parser->decode($body, true);
    } 
}
