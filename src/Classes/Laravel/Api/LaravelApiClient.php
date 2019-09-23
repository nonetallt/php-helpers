<?php

namespace Nonetallt\Helpers\Laravel\Api;

use Nonetallt\Helpers\Internet\Http\Clients\JsonHttpClient;
use Nonetallt\Helpers\Laravel\Api\Exception\LaravelApiExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;

class LaravelApiClient extends JsonHttpClient
{
    public function __construct()
    {
        parent::__construct();
        $this->setErrorAccessors('errors');
    }

    /**
     * @override
     */
    protected function createResponseExceptionFactory() : HttpRequestResponseExceptionFactory
    {
        return new LaravelApiExceptionFactory();
    }
}
