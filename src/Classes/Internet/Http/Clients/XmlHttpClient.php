<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use Nonetallt\Helpers\Internet\Http\Responses\Processors\JsonResponseParser;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\XmlResponseParser;

class XmlHttpClient extends HttpClient
{
    protected function overrideRequestSettings() : array
    {
        return [
            'response_parser' => new XmlResponseParser()
        ];
    }
}
