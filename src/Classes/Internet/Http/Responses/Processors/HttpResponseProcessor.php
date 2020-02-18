<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;

interface HttpResponseProcessor
{
    public function process(HttpResponse $response) : HttpResponse;
}
