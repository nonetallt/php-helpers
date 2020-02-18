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
use Nonetallt\Helpers\Internet\Http\Responses\GuzzleHttpResponseHandler;

/**
 * Wrapper class for common API usage that utilizes GuzzleHttp client for
 * requests.
 *
 */
class HttpClient
{
    protected $client;
    protected $statuses;

    /***
     * @param HttpStatusRepository $repo The repository that should be used
     * for fetching request status codes. Preferably an application wide
     * singleton saved in application container;
     *
     */
    public function __construct(HttpStatusRepository $repo = null)
    {
        $this->client = $this->createClient();
        $this->statuses = $repo ?? new HttpStatusRepository();
    }

    /**
     * Avoid serializing client since it might contain closures
     *
     */
    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), ['client']);
    }

    /**
     * Recreate client after deserialization
     *
     */
    public function __wakeup()
    {
        $this->client = $this->createClient();
    }

    /**
     * Create the client instance
     *
     */
    protected function createClient() : Client
    {
        return new Client([
            'handler' => HandlerStack::create(new CurlMultiHandler())
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
            $request = $this->beforeRequest($request);
            $response = $this->client->request($request->getMethod(), $request->getUrl(), $this->getRequestOptions($request));
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
                $request = $this->beforeRequest($request);
                return $this->client->requestAsync($request->getMethod(), $request->getUrl(), $this->getRequestOptions($request));
            };
        });

        $pool = new \GuzzleHttp\Pool($this->client, $guzzleRequests, [
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
     * Get request options for guzzle from a request object
     *
     */
    private function getRequestOptions(HttpRequest $request) : array
    {
        $onRedirect = function(RequestInterface $guzzleRequest, ResponseInterface $response, UriInterface $uri) use($request) {
            $status = $this->statuses->getByCode($response->getStatusCode());
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

        if($request->getSettings()->getSetting('timeout')->hasUsableValue()) {
            $requestOptions['timeout'] = $request->getSettings()->timeout;
        }

        return $requestOptions;
    }

    /**
     * Create response from guzzlehttp
     *
     */
    private function createResponse(HttpRequest $request, ?Response $guzzleResponse, RequestException $exception = null) : HttpResponse
    {
        $handler = new GuzzleHttpResponseHandler($guzzleResponse, $exception);
        $handler->setHttpStatusRepository($this->statuses);
        $response = $handler->createResponse($request);

        /* Run all request processors */
        foreach($request->getSettings()->request_processors as $processor) {
            $response = $processor->process($response, $handler);
        }

        return $response;
    }

    /**
     * Modify the outgoing request, ment to be overridden by child class
     *
     */
    protected function beforeRequest(HttpRequest $request) : HttpRequest
    {
        return $request;
    }
}
