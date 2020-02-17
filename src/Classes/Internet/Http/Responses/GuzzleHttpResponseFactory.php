<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Internet\Http\Common\HttpHeaderCollection;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatusRepository;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestConnectionException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestServerException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestResponseException;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponseBody;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\CreateResponseExceptions;
use Nonetallt\Helpers\Generic\Exceptions\ParsingException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Common\HttpHeader;

class GuzzleHttpResponseFactory
{
    public function __construct()
    {
    }

    public function createResponse(HttpRequest $request, ?Response $guzzleResponse, RequestException $exception = null) : HttpResponse
    {
        /* Attempt to get response from exception if response is not set */
        if($guzzleResponse === null && $exception !== null) {
            $guzzleResponse = $exception->getResponse();
        }

        $response = $this->createResponseClass($request, $guzzleResponse);
        $response->getExceptions()->pushAll($this->createConnectionExceptions($request, $exception)); 
        $response->getExceptions()->pushAll($this->createResponseExceptions($request, $response));

        return $response;
    }

    private function createResponseExceptions(HttpRequest $request, HttpResponse $response) : HttpRequestExceptionCollection
    {
        $settings = $request->getSettings();
        $proc = new CreateResponseExceptions($settings->error_accessor, $settings->error_message_accessor, $settings->response_exception_factory);
        return $proc->createExceptions($response);
    }

    private function createResponseClass(HttpRequest $request, ?Response $guzzleResponse) : HttpResponse
    {
        if($guzzleResponse === null) {
            return new HttpResponse($request);
        }

        $body = new HttpResponseBody($guzzleResponse->getBody(), $request->getSettings()->response_parser);

        $status = HttpStatusRepository::getInstance()->getByCode($guzzleResponse->getStatusCode());
        $headers = new HttpHeaderCollection();

        foreach($guzzleResponse->getHeaders() as $name => $values) {
            $headers->push(new HttpHeader($name, implode(', ', $values)));
        }

        return new HttpResponse($request, $body, $status, $headers);
    }

    private function createConnectionExceptions(HttpRequest $request, ?RequestException $previous) : HttpRequestExceptionCollection
    {
        $exceptions = new HttpRequestExceptionCollection();

        if($previous === null) {
            return $exceptions;
        }

        $curlErrorCode = $previous->getHandlerContext()['errno'] ?? 0;

        /* Handle curl errors if applicable */
        if($curlErrorCode !== 0) {
            $curlErrorMessage = curl_strerror($curlErrorCode);
            $msg = "$curlErrorMessage (connection error code $curlErrorCode)";
            $exceptions->push(new HttpRequestConnectionException($msg, $curlErrorCode, $previous));
        }

        if($previous instanceof BadResponseException) {
            $statusCode = $previous->getResponse()->getStatusCode();
            $statuses = HttpStatusRepository::getInstance();

            if(! $request->getSettings()->isErrorCodeIgnored($statusCode)) {

                $codeExists = $statuses->codeExists($statusCode);
                $statusName = $codeExists ? $statuses->getByCode($statusCode)->getName() : 'Unknown';

                $msg = "Server responded with code $statusCode ($statusName)";
                $exceptions->push(new HttpRequestServerException($msg, $statusCode, $previous));
            }
        }

        return $exceptions;
    }
}
