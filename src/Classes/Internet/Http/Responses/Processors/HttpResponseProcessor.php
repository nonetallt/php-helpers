<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use GuzzleHttp\Exception\RequestException;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;


interface HttpResponseProcessor
{
    /**
     * Modify the response as neccesary
     *
     */
    public function processHttpResponse(HttpResponse $response, ?RequestException $previous) : HttpResponse;
}
