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
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestConnectionException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestServerException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Nonetallt\Helpers\Internet\Http\Redirections\HttpRedirection;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatusRepository;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequestCollection;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponseCollection;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Generic\Exceptions\InvalidTypeException;

/**
 * Wrapper class for common API usage that utilizes GuzzleHttp client for
 * requests.
 */
class HttpClient
{
    private $client;
    private $auth;
    private $retryTimes;
    private $statuses;
    private $ignoredErrorCodes;

    public function __construct(int $retryTimes = 0, float $timeout = 10)
    {
        $this->auth = null;
        $this->retryTimes = $retryTimes;
        $this->ignoredErrorCodes = [];

        $handler = HandlerStack::create(new CurlMultiHandler());
        $handler->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));

        $this->client = new Client([
            'handler' => $handler,
            'timeout' => $timeout,
        ]);

        $this->statuses = new HttpStatusRepository();
    }

    public function setAuth($auth)
    {
        if(is_string($auth)) {
            $this->auth = $auth;
            return;
        }

        if(! is_array($auth)) {
            $given = (new DescribeObject($auth))->describeType();
            $msg = "Auth must be either a string or an array, $given given";
            throw new \InvalidArgumentException($msg);
        }

        if(! isset($auth[0])) {
            $msg = "Auth array must contain 0 index used as the username";
            throw new \InvalidArgumentException($msg);
        } 

        if(! isset($auth[1])) {
            $msg = "Auth array must contain 1 index used as the password";
            throw new \InvalidArgumentException($msg);
        }

        $this->auth = [$auth[0], $auth[1]];
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

    public function ignoreErrorCodes(array $codes)
    {
        foreach($codes as $code) {
            $this->ignoreErrorCode($code);
        }
    }

    public function ignoreErrorCode(int $code, bool $ignore = true)
    {
        if($code < 400 || $code > 499) {
            $msg = "Codes can only be ignored from the 4xx range, $code given";
            throw new \InvalidArgumentException($msg);
        }
        $this->ignoredErrorCodes[$code] = $ignore;
    }

    public function isCodeIgnored(int $code) : bool
    {
        return in_array($code, array_keys($this->ignoredErrorCodes));
    }

    public function sendRequests(HttpRequestCollection $requests) : HttpResponseCollection
    {
        $originalRequests = [];
        $promises = [];

        /* Create asynchronous requests for each requesst */
        foreach($requests as $index => $request) {
            $method = $request->getMethod();
            $url = $request->getUrl();
            $query = $this->requestOptions($request->getQuery(), $request);

            $originalRequests[] = $request;
            $promises[] = $this->client->requestAsync($method, $url, $query);
        }

        // Wait for the requests to complete, even if some of them fail
        $responses = Promise\settle($promises)->wait();
        $results = new HttpResponseCollection();

        foreach($responses as $index => $promise) {

            /* Get the request that has the same index as the promise */
            $originalRequest = $originalRequests[$index];
            $guzzleResponse = null;
            $exception = null;

            if($promise['state'] === 'fulfilled') {
                /* Successful request */
                $guzzleResponse = $promise['value'];
            }
            else {
                /* Failed request */
                $exception = $promise['reason'];
            }

            $response = $this->resolveResponse($originalRequest, $guzzleResponse, $exception);
            $results->push($response);
        }

        return $results;
    }

    public function sendRequest(HttpRequest $request) : HttpResponse
    {
        $method = $request->getMethod();
        $url = $request->getUrl();
        $query = $this->requestOptions($request->getQuery(), $request);
        $response = null;
        $exception = null;

        try {
            $response = $this->client->request($method, $url, $query);
        } 
        catch(RequestException $e) {
            $exception = $e;
        }

        return $this->resolveResponse($request, $response, $exception);
    }

    private function requestOptions(array $query, HttpRequest $requestWrapper)
    {
        $onRedirect = function(RequestInterface $request, ResponseInterface $response, UriInterface $uri) use ($requestWrapper) {
            $from = (string)$request->getUri();
            $to = (string)$uri;
            $code = $response->getStatusCode();
            $status = $this->statuses->getByCode($code);
            $redirection = new HttpRedirection($from, $to, $status);
            $requestWrapper->getRedirections()->push($redirection);
        };

        $requestOptions = [
            'query' => $query,
            'allow_redirects' => [
                'on_redirect' => $onRedirect
            ]
        ];

        /* If auth is set, append to request */
        if(is_array($this->auth)) $requestOptions['auth'] = $this->auth;
        if(is_string($this->auth)) $requestOptions['headers']['Authorization'] = $this->auth;

        return $requestOptions;
    }

    private function resolveResponse(HttpRequest $request, ?Response $response, ?RequestException $exception = null) : HttpResponse
    {
        if($exception instanceof ClientException) {
            /* Get proper response for 4xx errors from the exception */
            $response = $exception->getResponse();
            $code = $response->getStatusCode();
            $exception = $this->isCodeIgnored($code) ? null : $exception;
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

            if($this->statuses->codeExists($statusCode)) {
                $status = $this->statuses->getByCode($statusCode);
                $statusName = $status->getName();
            }

            $msg = "Server responded with code $statusCode ($statusName)";
            $exceptions->push(new HttpRequestServerException($msg, $statusCode, $previous));
        }

        return $exceptions;
    }
}
