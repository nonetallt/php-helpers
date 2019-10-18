<?php

namespace Nonetallt\Helpers\Internet\Http\Requests;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;
use Nonetallt\Helpers\Internet\Http\Redirections\HttpRedirectionCollection;
use Nonetallt\Helpers\Internet\Http\Common\HttpHeaderCollection;
use Nonetallt\Helpers\Internet\Http\Common\HttpHeader;
use Nonetallt\Helpers\Internet\Http\HttpQuery;


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
    private $ignoredErrorCodes;

    public function __construct(string $method, string $url, array $query = [], string $body = null, $headers = null)
    {
        $this->setMethod($method);
        $this->setUrl($url);
        $this->setQuery($query);
        $this->setBody($body);
        $this->setHeaders($headers);
        $this->redirections = new HttpRedirectionCollection();
        $this->ignoredErrorCodes = [];
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
}
