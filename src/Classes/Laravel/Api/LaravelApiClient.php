<?php

namespace Nonetallt\Helpers\Laravel\Api;

use Nonetallt\Helpers\Internet\Http\Clients\HttpApiClient;
use Nonetallt\Helpers\Laravel\Api\Exception\LaravelApiExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Generic\Traits\LazyLoadsProperties;

class LaravelApiClient extends HttpApiClient
{
    use LazyLoadsProperties;

    public function __construct()
    {
        parent::__construct();
    }

    public function lazyLoadResponseExceptionFactory()
    {
        return new LaravelApiExceptionFactory();
    }

    protected function modifyRequest(HttpRequest $request)
    {
        $settings = $request->getResponseSettings();
        $settings->setResponseExceptionFactory($this->responseExceptionFactory);
        $settings->setErrorAccessors('errors');

        return $request;
    }
}
