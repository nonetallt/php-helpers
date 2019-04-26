<?php

namespace Nonetallt\Helpers\Internet\Http;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

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

    public function __construct(string $method, string $url, array $query = [])
    {
        $this->setMethod($method);
        $this->setUrl($url);
        $this->setQuery($query);
    }

    public static function arrayValidationRules()
    {
        return [
            'method' => 'required|string',
            'url' => 'required|string',
            'query' => 'array'
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setMethod(string $method)
    {
        $method = strtoupper($method);
        in_array_required($method, self::HTTP_METHODS);
        $this->method = $method;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function setQuery(array $query)
    {
        $this->query = $query;
    }

    public function addToQuery(array $data)
    {
        $this->query = array_merge($this->query, $data);
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function getQuery() : array
    {
        return $this->query;
    }

    public function toArray() : array
    {
        return [
            'method' => $this->method,
            'url'    => $this->url,
            'query'  => $this->query,
        ];
    }
}
