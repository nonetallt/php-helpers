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
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestResponseException;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponseBody;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\CreateResponseExceptions;

class GuzzleHttpResponseFactory
{
    private $exceptions;

    public function __construct()
    {
        $this->resetExceptions();
    }

    public function createResponse(HttpRequest $request, ?Response $guzzleResponse, RequestException $exception = null) : HttpResponse
    {
        $this->resetExceptions();

        /* Attempt to get response from exception if response is not set */
        if($guzzleResponse === null && $exception !== null) {
            $guzzleResponse = $exception->getResponse();
        }

        $this->exceptions->pushAll($this->createConnectionExceptions($request, $exception)); 
        $response = $this->createResponseClass($request, $guzzleResponse);

        $settings = $request->getResponseSettings();
        $proc = new CreateResponseExceptions($settings->error_accessor, $settings->error_message_accessor, $settings->response_exception_factory);
        $this->exceptions->pushAll($proc->createExceptions($response));

        $response->getExceptions()->pushAll($this->exceptions);

        return $response;
    }

    private function resetExceptions()
    {
        $this->exceptions = new HttpRequestExceptionCollection();
    }

    private function createResponseClass(HttpRequest $request, ?Response $guzzleResponse) : HttpResponse
    {
        if($guzzleResponse === null) {
            return new HttpResponse($request);
        }

        $body = $this->getResponseBody($request, $guzzleResponse->getBody());
        $status = HttpStatusRepository::getInstance()->getByCode($guzzleResponse->getStatusCode());
        $headers = new HttpHeaderCollection();

        return new HttpResponse($request, $body, $status, $headers);
    }

    private function getResponseBody($request, $body)
    {
        try {
            $parser = $request->getResponseSettings()->response_parser;
            $body = new HttpResponseBody($body, $parser);
            $body->getParsed();
        }
        catch(ParsingException $e) {
            $msg = "Response could not be parsed";
            $this->exceptions->push(new HttpRequestResponseException($msg, 0, $e));
        }

        return $body;
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

            if(! $request->getResponseSettings()->isErrorCodeIgnored($statusCode)) {

                $codeExists = $statuses->codeExists($statusCode);
                $statusName = $codeExists ? $statuses->getByCode($statusCode)->getName() : 'Unknown';

                $msg = "Server responded with code $statusCode ($statusName)";
                $exceptions->push(new HttpRequestServerException($msg, $statusCode, $previous));
            }
        }

        return $exceptions;
    }
}
