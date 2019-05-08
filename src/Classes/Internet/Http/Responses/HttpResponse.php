<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use GuzzleHttp\Psr7\Response;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestException;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;

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
    public function __construct(HttpRequest $originalRequest, ?Response $response, HttpRequestExceptionCollection $exceptions)
    {
        $this->originalRequest = $originalRequest;
        $this->response = $response;
        $this->setExceptions($exceptions);
    }

    public function addException(HttpRequestException $exception)
    {
        $this->exceptions->push($exception);
    }

    public function setExceptions(?HttpRequestExceptionCollection $exceptions)
    {
        if($exceptions === null) $exceptions = new HttpRequestExceptionCollection();
        $this->exceptions = $exceptions;
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
            if($this->response !== null) $this->body = (string)$this->response->getBody();
            else $this->body = '';
        }

        return  $this->body;
    }

    public function getHeaders() : array
    {
        return $this->response->getHeaders();
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
