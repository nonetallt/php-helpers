<?php

namespace Nonetallt\Helpers\Laravel\Api;

use Nonetallt\Helpers\Internet\Http\Responses\Processors\JsonResponseParser;
use Nonetallt\Helpers\Laravel\Api\Exception\LaravelApiExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Clients\JsonHttpClient;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;

class LaravelHttpClient extends JsonHttpClient
{
    private $exceptionFactory;

    protected function beforeRequest(HttpRequest $request) : HttpRequest
    {
        $request = parent::beforeRequest($request);
        $request->getSettings()->setAll([
            'error_accessor' => 'errors',
            'response_exception_factory' => new LaravelApiExceptionFactory()
        ]);

        return $request;
    }
}
