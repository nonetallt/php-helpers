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
use Nonetallt\Helpers\Internet\Http\Responses\GuzzleHttpResponseFactory;

/**
 * Wrapper class for common API usage that utilizes GuzzleHttp client for
 * requests.
 */
class HttpClient
{
    protected $guzzle;
    protected $factory;

    public function __construct()
    {
        $this->guzzle = $this->createGuzzleClient();
        $this->factory = new GuzzleHttpResponseFactory();
    }

    /**
     * Avoid serializing guzzle client since it contains closures
     *
     */
    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), ['guzzle']);
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
    protected function createGuzzleClient() : Client
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

        return $this->factory->createResponse($request, $response, $exception);
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
                $response = $this->factory->createResponse($requests[$index], $response);
                $responses->push($response);
            },
            'rejected' => function($exception, $index) use ($requests, &$responses) {
                $response = $this->factory->createResponse($requests[$index], null, $exception);
                $responses->push($response);
            }
        ]);

        /* Wait for requests to resolve */
        $pool->promise()->wait();

        return $responses;
    }

    /**
     * Get request options for guzzle from a request object
     *
     */
    protected function getRequestOptions(HttpRequest $request) : array
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
