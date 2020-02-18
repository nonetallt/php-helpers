<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponseBody;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatusRepository;
use Nonetallt\Helpers\Internet\Http\Common\HttpHeaderCollection;
use Nonetallt\Helpers\Internet\Http\Common\HttpHeader;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestConnectionException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestServerException;

class GuzzleHttpResponseHandler implements HttpResponseHandler
{
    private $response;
    private $exception;
    private $statuses;

    public function __construct(?Response $response, RequestException $exception = null)
    {
        /* Attempt to get response from exception if response is not set */
        if($response === null && $exception !== null) {
            $response = $exception->getResponse();
        }

        $this->response = $response;
        $this->exception = $exception;
    }

    public function setHttpStatusRepository(HttpStatusRepository $repository)
    {
        $this->statuses = $repository;
    }

    public function createResponse(HttpRequest $request) : HttpResponse
    {
        if($this->response === null) {
            return new HttpResponse($request);
        }

        /* Create response body */
        $body = new HttpResponseBody($this->response->getBody(), $request->getSettings()->response_parser);

        /* Create status code */
        $status = $this->statuses->getByCode($this->response->getStatusCode());

        /* Create response headers */
        $headers = new HttpHeaderCollection();
        foreach($this->response->getHeaders() as $name => $values) {
            $headers->push(new HttpHeader($name, implode(', ', $values)));
        }

        return new HttpResponse($request, $body, $status, $headers);
    }

    public function getConnectionExceptions(HttpRequest $request) : HttpRequestExceptionCollection
    {
        $exceptions = new HttpRequestExceptionCollection();

        if($this->exception === null) {
            return $exceptions;
        }

        $curlErrorCode = $this->exception->getHandlerContext()['errno'] ?? 0;

        /* Handle curl errors if applicable */
        if($curlErrorCode !== 0) {
            $curlErrorMessage = curl_strerror($curlErrorCode);
            $msg = "$curlErrorMessage (connection error code $curlErrorCode)";
            $exceptions->push(new HttpRequestConnectionException($msg, $curlErrorCode, $this->exception));
        }

        if($this->exception instanceof BadResponseException) {
            $statusCode = $this->exception->getResponse()->getStatusCode();

            if(! $request->getSettings()->isErrorCodeIgnored($statusCode)) {

                $codeExists = $this->statuses->codeExists($statusCode);
                $statusName = $codeExists ? $this->statuses->getByCode($statusCode)->getName() : 'Unknown';

                $msg = "Server responded with code $statusCode ($statusName)";
                $exceptions->push(new HttpRequestServerException($msg, $statusCode, $this->exception));
            }
        }

        return $exceptions;
    }
}
