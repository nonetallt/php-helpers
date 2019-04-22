<?php

namespace App\Domain\Api;

use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Wrapper class for common API usage that utilizes GuzzleHttp client for
 * requests.
 */
class HttpClient
{
    private $client;
    private $auth;
    private $retryTimes;

    public function __construct(int $retryTimes = 0, float $timeout = 10, float $connectTimeout = 10, float $readTimeout = 10)
    {
        $this->auth = null;
        $this->retryTimes = $retryTimes;

        $handler = HandlerStack::create(new CurlMultiHandler());
        $handler->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));

        $this->client = new Client([
            'handler'         => $handler,
            'timeout'         => $timeout,
            'connect_timeout' => $connectTimeout,
            'read_timeout'    => $readTimeout
        ]);
    }

    public function setAuth(string $user, string $password)
    {
        $this->auth = [$user, $password];
    }

    private function retryDecider()
    {
        return function ($retries, Request $request, Response $response = null, RequestException $exception = null) {
            /* \Illuminate\Support\Facades\Log::debug("retry decider $retries times"); */

            // Limit the number of retries 
            if ($retries >= $this->retryTimes) return false;

            // Retry connection exceptions
            if ($exception instanceof ConnectException) return true;

            return false;
        };
    }

    private function retryDelay()
    {
        return function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        };
    }

    public function sendRequests(HttpRequestCollection $requests) : HttpResponseCollection
    {
        $originalRequests = [];
        $promises = [];

        /* Create asynchronous requests for each requesst */
        foreach($requests as $index => $request) {
            $method = $request->getMethod();
            $url = $request->getUrl();
            $query = $this->requestOptions($request->getQuery());

            $originalRequests[] = $request;
            $promises[] = $this->client->requestAsync($method, $url, $query);
        }

        // Wait for the requests to complete, even if some of them fail
        $responses = Promise\settle($promises)->wait();

        $results = new HttpResponseCollection();

        foreach($responses as $index => $promise) {

            /* Get the request that has the same index as the promise */
            $originalRequest = $originalRequests[$index];

            /* Successful request */
            if($promise['state'] === 'fulfilled') {
                $results->push($this->response($originalRequest, $promise['value']));
                continue;
            }

            /* Failed request */
            $response = new JsonApiResponse($originalRequest);
            $response->addError($promise['reason']->getMessage());
            $results->push($response);
        }

        return $results;
    }

    public function sendRequest(HttpRequest $request) : HttpResponse
    {
        $method = $request->getMethod();
        $url = $request->getUrl();
        $query = $this->requestOptions($request->getQuery());

        try {
            $response = $this->client->request($method, $url, $query);
            return $this->response($request, $response);
        } 
        catch(RequestException $e) {
            $response = new JsonApiResponse($request);
            $response->addError($e->getMessage());
            return $response;
        }
    }

    private function requestOptions(array $query)
    {
        $requestOptions = [
            'query' => $query
        ];

        /* If auth is set, append to request */
        if($this->auth !== null) $requestOptions['auth'] = $this->auth;

        return $requestOptions;
    }

    private function response(HttpRequest $request, Response $response)
    {
        $response = $this->createResponse($request, $response);
        $expected = HttpResponse::class;

        if(! is_a($response, $expected, false)) {
            $msg = "Unexpected return value from createResponse(), expected $expected";
            throw new \Exception($msg);
        }

        return $response;
    }

    protected function createResponse(HttpRequest $request, Response $response)
    {
        $response = new HttpResponse($request, $response);
        return $response;
    }
}
