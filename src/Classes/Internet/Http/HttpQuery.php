<?php

namespace Nonetallt\Helpers\Internet\Http;

class HttpQuery
{
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public static function fromUrl(string $url) : self
    {
        $query = parse_url($url)['query'] ?? '';
        return static::fromString($query);
    }

    public static function fromString(string $value) : self
    {
        $params = [];
        foreach(explode('&', $value) as $param) {
            $parts = explode('=', $param);

            if(count($parts) !== 2) {
                return "Syntax error: missing '='";
            }

            $key = $parts[0];
            $value = $parts[1];

            $params[$key] = $value;
        }

        return new self($params);
    }

    public function __toString()
    {
        $string = '';
        foreach($this->parameters as $key => $value) {
            $key = urlencode($key);
            $value = urlencode($value);
            $leader = $string === '' ? '?' : '&';
            $string .= "$leader$key=$value"; 
        }

        return $string;
    }

    public function getBody() : string
    {
        return substr((string)$this, 1);
    }

    public function getQueryString() : string
    {
        return (string) $this;
    }

    public function toArray() : array
    {
        return $this->parameters;
    }

    public function addParameter(array $data)
    {
        $this->parameters += $data;
    }

    public function getParameter(string $key)
    {
        return $this->parameters[$key] ?? null;
    }

    public function hasParameter(string $key) : bool
    {
        return isset($this->parameters[$key]);
    }
}
