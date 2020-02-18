<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatusRepository;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;

interface HttpResponseHandler
{
    public function createResponse(HttpRequest $request) : HttpResponse;

    public function getConnectionExceptions(HttpRequest $request) : HttpRequestExceptionCollection;

    public function setHttpStatusRepository(HttpStatusRepository $repository);
}
