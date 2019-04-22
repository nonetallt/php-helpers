<?php

namespace Nonetallt\Helpers\Internet\Http;

use GuzzleHttp\Psr7\Response;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestException;

class HttpResponse
{
    private $body;
    private $originalRequest;
    private $response;
    protected $exceptions;

    /**
     * @param App\Domain\Api\HttpRequest $originalRequest Request that got this
     * response.
     *
     * @param GuzzleHttp\Psr7\Response $response can be null for unfulfilled
     * requests.
     */
    public function __construct(HttpRequest $originalRequest, ?Response $response = null)
    {
        $this->originalRequest = $originalRequest;
        $this->response = $response;
        $this->exceptions = new HttpRequestExceptionCollection();
    }

    public function addException(HttpRequestException $exception)
    {
        $this->exceptions->push($exception);
    }

    /**
     * Proxy for getExceptionMessages()
     */
    public function getErrors() : array 
    {
        return $this->getExceptionMessages();
    }

    public function getExceptionMessages() : array
    {
        return $this->exceptions->map(function($exception) {
            return $exception->getMessage();
        });
    }

    public function getExceptions() : HttpRequestExceptionCollection
    {
        return $this->exceptions;
    }

    public function getOriginalRequest() : HttpRequest
    {
        if(is_null($this->originalRequest)) throw new \Exception("Original message is not set");
        return $this->originalRequest;
    }

    public function getBody() : string
    {
        if($this->body === null) {
            if($this->response !== null) $this->body = (string)$response->getBody();
            else $this->body = '';
        }

        return  $this->body;
    }

    /**
     * Proxy for hasExceptions()
     */
    public function hasErrors() : bool
    {
        return $this->hasExceptions();
    }

    public function hasExceptions() : bool
    {
        return ! $this->exceptions->isEmpty();
    }

    public function isSuccessful() : bool
    {
        return $this->exceptions->isEmpty();
    }
}
