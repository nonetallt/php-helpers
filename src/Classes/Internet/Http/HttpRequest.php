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
    private $extraData;

    public function __construct(string $method, string $url, array $query = [])
    {
        $this->setMethod($method);
        $this->url = $url;
        $this->query = $query;
        $this->extraData = [];
    }

    public function addExtra(string $key, $value)
    {
        $this->extraData[$key] = $value;
    }

    public function getExtra(string $key)
    {
        $value = $this->extraData[$key] ?? null;
        return $value;
    }

    public static function arrayValidationRules()
    {
        return [
            'method' => 'required|string',
            'url' => 'required|string',
            'query' => 'array'
        ];
    }

    public function setMethod(string $method)
    {
        $method = strtoupper($method);
        in_array_required($method, self::HTTP_METHODS);
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function toArray()
    {
        return [
            'method' => $this->method,
            'url'    => $this->url,
            'query'  => $this->query,
        ];
    }
}
