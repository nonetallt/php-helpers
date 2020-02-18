<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use Nonetallt\Helpers\Internet\Http\Responses\Processors\JsonResponseParser;

class JsonHttpClient extends HttpClient
{
    protected function overrideRequestSettings() : array
    {
        return [
            'response_parser' => new JsonResponseParser()
        ];
    }
}
