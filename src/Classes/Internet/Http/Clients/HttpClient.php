<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;


use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;

use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestConnectionException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestServerException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Redirections\HttpRedirection;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatusRepository;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequestCollection;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponseCollection;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Generic\Exceptions\InvalidTypeException;
use Nonetallt\Helpers\Internet\Http\Requests\Processors\HttpRequestProcessorCollection;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\HttpResponseProcessorCollection;

/**
 * Wrapper class for common API usage that utilizes GuzzleHttp client for
 * requests.
 */
class HttpClient
{
    private $guzzle;

    private $requestProcessors;
    private $responseProcessors;

    public function __construct()
    {
        $this->requestProcessors = new HttpRequestProcessorCollection();
        $this->responseProcessors = new HttpResponseProcessorCollection();

        $this->guzzle = new Client([
            'handler' => HandlerStack::create(new CurlMultiHandler()),
            'timeout' => 10,
        ]);
    }

    /**
     * Send a single http request and wait for a response
     *
     */
    public function sendRequest(HttpRequest $request) : HttpResponse
    {
        $response = null;
        $exception = null;

        try {
            $response = $this->guzzle->request($request->getMethod(), $request->getUrl(), $request->getRequestOptions());
        } 
        catch(RequestException $e) {
            $exception = $e;
        }

        return $this->resolveResponse($request, $response, $exception);
    }

    /**
     * Send multiple http requests and wait for responses 
     *
     */
    public function sendRequests(HttpRequestCollection $requests, int $concurrency = null) : HttpResponseCollection
    {
        $responses = new HttpResponseCollection();

        /* Map requests to promises */
        $guzzleRequests = $requests->map(function($request) {
            return function() use ($request) {
                return $this->guzzle->requestAsync($request->getMethod(), $request->getUrl(), $request->getRequestOptions());
            };
        });

        $pool = new \GuzzleHttp\Pool($this->guzzle, $guzzleRequests, [
            'concurrency' => $concurrency ?? $requests->count(),
            'fulfilled' => function($response, $index) use ($requests, &$responses) {
                $response = $this->resolveResponse($requests[$index], $response);
                $responses->push($response);
            },
            'rejected' => function($exception, $index) use ($requests, &$responses) {
                $response = $this->resolveResponse($requests[$index], null, $exception);
                $responses->push($response);
            }
        ]);

        /* Wait for requests to resolve */
        $pool->promise()->wait();

        return $responses;
    }

    private function resolveResponse(HttpRequest $request, ?Response $response, ?RequestException $exception = null) : HttpResponse
    {
        if($exception instanceof ClientException) {
            /* Get proper response for 4xx errors from the exception */
            $response = $exception->getResponse();
            $code = $response->getStatusCode();
            $exception = $request->isCodeIgnored($code) ? null : $exception;
        }

        return $this->createResponse($request, $response, $exception);
    }

    /**
     * Handle response from GuzzleHttp library and create a HttpResponse
     * wrapper object
     *
     * @param Nonetallt\Helpers\Internet\Http\HttpRequest $request Original request that was sent
     * @param GuzzleHttp\Psr7\Response $response Response received from Guzzlehttp
     * @param GuzzleHttp\Exception\RequestException $exception Exception received from Guzzlehttp
     *
     * @return App\Domain\HttpResponse $response Created response wrapper
     *
     */
    protected function createResponse(HttpRequest $request, ?Response $response, ?RequestException $exception = null) : HttpResponse
    {
        $exceptions = $this->createConnectionExceptions($exception);
        return new HttpResponse($request, $response, $exceptions);
    }

    /**
     * Wrap guzzle connection exceptions into a collection
     *
     * @param GuzzleHttp\Exception\RequestException $previous Connection exception
     *
     * @return Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection $exceptions
     *
     */
    protected function createConnectionExceptions(?RequestException $previous) : HttpRequestExceptionCollection
    {
        $exceptions = new HttpRequestExceptionCollection();

        /* No exception, do not add errors */
        if($previous === null) return $exceptions;

        $curlErrorCode = $previous->getHandlerContext()['errno'] ?? 0;

        /* Handle curl errors if applicable */
        if($curlErrorCode !== 0) {
            $curlErrorMessage = curl_strerror($curlErrorCode);
            $msg = "$curlErrorMessage (connection error code $curlErrorCode)";
            $exceptions->push(new HttpRequestConnectionException($msg, $curlErrorCode, $previous));
            return $exceptions;
        }

        if($previous instanceof BadResponseException) {
            $statusCode = $previous->getResponse()->getStatusCode();
            $statusName = 'Unknown Status';
            $statuses = HttpStatusRepository::getInstance();

            if($statuses->codeExists($statusCode)) {
                $status = $statuses->getByCode($statusCode);
                $statusName = $status->getName();
            }

            $msg = "Server responded with code $statusCode ($statusName)";
            $exceptions->push(new HttpRequestServerException($msg, $statusCode, $previous));
        }

        return $exceptions;
    }
}
