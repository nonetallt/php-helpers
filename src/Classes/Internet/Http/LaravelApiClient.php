<?php

namespace App\Domain\Api;

class LaravelApiClient extends HttpClient
{
    protected function createResponse(HttpRequest $request, \GuzzleHttp\Psr7\Response $response)
    {
        $response = new JsonApiResponse($request, $response);
        $response->setErrorAccessors('errors');
        return $response;
    }
}
