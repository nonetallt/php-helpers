<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequestCollection;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponseCollection;

use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatusRepository;
use Nonetallt\Helpers\Internet\Http\Redirections\HttpRedirection;

/**
 * Wrapper class for common API usage that utilizes GuzzleHttp client for
 * requests.
 */
class HttpClient
{
    private $guzzle;

    public function __construct()
    {
        $this->guzzle = $this->createGuzzleClient();
    }

    /**
     * Avoid serializing guzzle client since it contains closures
     *
     */
    public function __sleep()
    {
        return [];
    }

    /**
     * Recreate client after deserialization
     *
     */
    public function __wakeup()
    {
        $this->guzzle = $this->createGuzzleClient();
    }

    /**
     * Create the guzzle client instance
     *
     */
    private function createGuzzleClient() : Client
    {
        return new Client([
            'handler' => HandlerStack::create(new CurlMultiHandler()),
            'timeout' => 10
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
            $response = $this->guzzle->request($request->getMethod(), $request->getUrl(), $this->getRequestOptions($request));
        } 
        catch(RequestException $e) {
            $exception = $e;
        }

        return $this->createResponse($request, $response, $exception);
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
                return $this->guzzle->requestAsync($request->getMethod(), $request->getUrl(), $this->getRequestOptions($request));
            };
        });

        $pool = new \GuzzleHttp\Pool($this->guzzle, $guzzleRequests, [
            'concurrency' => $concurrency ?? $requests->count(),
            'fulfilled' => function($response, $index) use ($requests, &$responses) {
                $response = $this->createResponse($requests[$index], $response);
                $responses->push($response);
            },
            'rejected' => function($exception, $index) use ($requests, &$responses) {
                $response = $this->createResponse($requests[$index], null, $exception);
                $responses->push($response);
            }
        ]);

        /* Wait for requests to resolve */
        $pool->promise()->wait();

        return $responses;
    }

    /**
     * Handle response from GuzzleHttp library and create a HttpResponse
     *
     */
    protected function createResponse(HttpRequest $request, ?Response $guzzleResponse, ?RequestException $exception = null) : HttpResponse
    {
        /* Attempt to get response from exception if response is not set */
        if($guzzleResponse === null && $exception !== null) {
            $guzzleResponse = $exception->getResponse();
        }

        $response = new HttpResponse($request, $guzzleResponse);

        foreach($request->getResponseProcessors() as $processor) {
            $response = $processor->processHttpResponse($response, $exception);
        }

        return $response;
    }

    /**
     * Get request options for guzzle from a request object
     *
     */
    private function getRequestOptions(HttpRequest $request) : array
    {
        $onRedirect = function(RequestInterface $guzzleRequest, ResponseInterface $response, UriInterface $uri) use($request) {
            $status = HttpStatusRepository::getInstance()->getByCode($response->getStatusCode());
            $redirection = new HttpRedirection((string)$guzzleRequest->getUri(), (string)$uri, $status);
            $request->getRedirections()->push($redirection);
        };

        $requestOptions = [
            'body' => $request->getBody(),
            'query' => $request->getQuery()->toArray(),
            'allow_redirects' => [
                'on_redirect' => $onRedirect
            ]
        ];

        /* Set all custom headers */
        foreach($request->getHeaders() as $header) {
            $requestOptions['headers'][$header->getName()] = $header->getValue();
        }

        return $requestOptions;
    }
}
