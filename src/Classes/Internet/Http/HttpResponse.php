<?php

namespace App\Domain\Api;

use App\Domain\Api\HttpRequest;
use GuzzleHttp\Psr7\Response;

class HttpResponse
{
    private $body;
    private $originalRequest;
    protected $errors;

    /**
     * @param App\Domain\Api\HttpRequest $originalRequest Request that got this
     * response.
     *
     * @param GuzzleHttp\Psr7\Response $response can be null for unfulfilled
     * requests.
     */
    public function __construct(HttpRequest $originalRequest, Response $response = null)
    {
        $this->originalRequest = $originalRequest;
        $this->body = '';
        $this->errors = [];

        if(! is_null($response)) $this->body = $response->getBody();
    }

    public function addError(string $message)
    {
        $this->errors[] = $message;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getOriginalRequest()
    {
        if(is_null($this->originalRequest)) throw new \Exception("Original message is not set");
        return $this->originalRequest;
    }

    public function getBody()
    {
        return (string)$this->body;
    }

    public function hasErrors()
    {
        return ! empty($this->getErrors());
    }
}
