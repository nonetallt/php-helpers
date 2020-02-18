<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponseHandler;

interface HttpResponseProcessor
{
    public function process(HttpResponse $response, HttpResponseHandler $handler) : HttpResponse;
}
