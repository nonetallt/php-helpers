<?php

namespace Nonetallt\Helpers\Laravel\Api;

use Nonetallt\Helpers\Internet\Http\Responses\Processors\JsonResponseParser;
use Nonetallt\Helpers\Laravel\Api\Exception\LaravelApiExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Clients\HttpClient;

class LaravelHttpClient extends HttpClient
{
    private $exceptionFactory;

    public function getClientSettings() : array
    {
        return [
            'error_accessor' => 'errors',
            'response_parser' => new JsonResponseParser(),
            'response_exception_factory' => new LaravelApiExceptionFactory()
        ];
    }
}
