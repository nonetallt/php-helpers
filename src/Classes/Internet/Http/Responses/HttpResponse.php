<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatus;
use Nonetallt\Helpers\Internet\Http\Common\HttpHeaderCollection;

class HttpResponse
{
    private $request;
    private $body;
    private $status;
    private $headers;
    protected $exceptions;

    public function __construct(HttpRequest $request, HttpResponseBody $body = null, HttpStatus $status = null, HttpHeaderCollection $headers = null)
    {
        $this->request = $request;
        $this->body = $body;
        $this->headers = $headers;
        $this->status = $status;
        $this->exceptions = new HttpRequestExceptionCollection();
    }

    public function getErrors() : array 
    {
        return $this->exceptions->getMessages();
    }

    public function getExceptions() : HttpRequestExceptionCollection
    {
        return $this->exceptions;
    }

    public function getRequest() : HttpRequest
    {
        return $this->request;
    }

    public function getBody() : ?HttpResponseBody
    {
        return $this->body;
    }

    public function getHeaders() : ?HttpHeaderCollection
    {
        return $this->headers;
    }

    public function getStatus() : ?HttpStatus
    {
        return $this->status;
    }

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
