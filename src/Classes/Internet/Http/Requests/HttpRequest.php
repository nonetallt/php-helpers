<?php

namespace Nonetallt\Helpers\Internet\Http\Requests;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;
use Nonetallt\Helpers\Internet\Http\Redirections\HttpRedirectionCollection;
use Nonetallt\Helpers\Internet\Http\Common\HttpHeaderCollection;
use Nonetallt\Helpers\Internet\Http\HttpQuery;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\CreateConnectionExceptions;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;

/**
 * Wrapper class for http request information
 */
class HttpRequest
{
    use ConstructedFromArray;

    const HTTP_METHODS = [
        'GET',
        'POST',
        'DELETE',
        'PUT',
        'PATCH'
    ];

    private $method;
    private $url;
    private $query;
    private $body;
    private $redirections;
    private $settings;

    public function __construct(string $method, string $url, array $query = [], string $body = null, $headers = null)
    {
        $this->setMethod($method);
        $this->setUrl($url);
        $this->setQuery($query);
        $this->setBody($body);
        $this->setHeaders($headers);
        $this->redirections = new HttpRedirectionCollection();
        $this->settings = new HttpRequestSettings();
    }

    public function setMethod(string $method)
    {
        $method = strtoupper($method);

        if(! in_array($method, self::HTTP_METHODS)) {
            $msg = "'$method' is not a valid http method";
            throw new \InvalidArgumentException($msg);
        }

        $this->method = $method;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function setQuery(array $query)
    {
        $this->query = new HttpQuery($query);
    }

    public function setHeaders($headers)
    {
        /* If null, default to empty array */
        if($headers === null) $headers = [];

        /* If array, convert to header collection */
        if(is_array($headers)) {
            $headers = HttpHeaderCollection::fromArray($headers);
        }
        
        $class = HttpHeaderCollection::class;
        if(! is_a($headers, $class)) {
            $given = (new DescribeObject($headers))->describeType();
            $msg = "Headers must be one of the following: null, array or $class, $given given";
            throw new \InvalidArgumentException($msg);
        }

        $this->headers = $headers;
    }

    public function getSettings() : HttpRequestSettings
    {
        return $this->settings;
    }

    public function getQuery() : HttpQuery
    {
        return $this->query;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function setBody(?string $body)
    {
        if($body === null) $body = '';
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders() : HttpHeaderCollection
    {
        return $this->headers;
    }

    public function getEffectiveUrl() : string
    {
        if($this->redirections->isEmpty()) return $this->getUrl(); 

        /* Return last redirection location */
        return (string)$this->redirections[$this->redirections->count() -1]->getTo();
    }

    public function getRedirections() : HttpRedirectionCollection
    {
        return $this->redirections;
    }
}
