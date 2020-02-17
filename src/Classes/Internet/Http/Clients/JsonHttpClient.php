<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use Nonetallt\Helpers\Internet\Http\Responses\Processors\JsonResponseParser;

class JsonHttpClient extends HttpClient
{
    public function getClientSettings() : array
    {
        return [
            'response_parser' => new JsonResponseParser()
        ];
    }
}
