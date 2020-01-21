<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

interface ResponseParser
{
    /**
     * @throws Nonetallt\Helpers\Generic\Exceptions\ParsingException
     *
     */
     public function parseResponseBody(string $body) : array;
}
