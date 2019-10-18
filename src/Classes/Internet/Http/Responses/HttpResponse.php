<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use GuzzleHttp\Psr7\Response;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestException;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatus;

class HttpResponse
{
    private $body;
    private $request;
    private $response;
    protected $exceptions;

    public function __construct(HttpRequest $request, ?Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->exceptions = new HttpRequestExceptionCollection();
    }

    public function setExceptions(HttpRequestExceptionCollection $exceptions)
    {
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

    public function getRequest() : HttpRequest
    {
        return $this->request;
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

    public function getStatus() : HttpStatus
    {
        $code = $this->response->getStatusCode();
        $status = HttpStatusRepository::getInstance()->getByCode($code);
        return $status;
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

    public function hasBody() : bool
    {
        return $this->getBody() !== '';
    }

    public function isSuccessful() : bool
    {
        return $this->exceptions->isEmpty();
    }
}
