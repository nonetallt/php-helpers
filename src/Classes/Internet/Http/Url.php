<?php

namespace Nonetallt\Helpers\Internet\Http;

use Nonetallt\Helpers\Generic\Container;

class Url
{
    const OPTIONS = [
        'scheme',
        'host',
        'port',
        'user',
        'pass',
        'path',
        'query',
        'fragment',
    ];

    const DEFAULTS = [
        'scheme' => 'http',
        'path' => '',
        'query' => ''
    ];

    CONST VALIDATORS = [
        'scheme'   => 'in:http,https',
        'host'     => 'string',
        'port'     => 'integer|min:1|max:65535',
        'user'     => 'string',
        'pass'     => 'string',
        'path'     => 'string',
        'query'    => 'string',
        'fragment' => 'string'
    ];

    private $attributes;

    public function __construct(array $options)
    {
        $this->attributes = new Container($options, self::DEFAULTS, self::VALIDATORS, self::OPTIONS);
    }

    public function __toString()
    {
        $data = $this->attributes->toArray();
        return http_build_url($data);
    }

    public static function fromString(string $string) : Url
    {
        $parsed = parse_url($string);

        if($parsed === false) {
            $msg = "Could not parse malformed url string '$string'";
            throw new \InvalidArgumentException($msg);
        }

        /* For some reason, simple domain names like test.com will be interpreted as path 
           and hostname will be left empty by parse_url, if this happens, use path as hostname 
         */
        if(! isset($parsed['host'])) {
            $parsed['host'] = $parsed['path'] ?? null;
            if($parsed['host'] === null) {
                $msg = "Could not parse hostname from string '$string'";
                throw new \InvalidArgumentException($msg);
            }
            unset($parsed['path']);
        }

        return new Url($parsed);
    }

    public function getScheme()
    {
        return $this->attributes->scheme;
    }

    public function getUser()
    {
        return $this->attributes->user;
    }

    public function getPassword()
    {
        return $this->attributes->pass;
    }

    public function getHost()
    {
        return $this->attributes->host;
    }

    public function getPort()
    {
        return $this->attributes->port;
    }

    public function getPath()
    {
        return $this->attributes->path;
    }

    public function getQueryString()
    {
        return $this->attributes->query;
    }

    public function getQuery() : HttpQuery
    {
        return HttpQuery::fromString($this->attributes->query);
    }

    public function toArray() : array
    {
        return [
            'scheme'       => $this->attributes->scheme,
            'host'         => $this->attributes->host,
            'port'         => $this->attributes->port,
            'user'         => $this->attributes->user,
            'password'     => $this->attributes->pass,
            'path'         => $this->attributes->path,
            'query'        => $this->getQuery()->toArray(),
            'query_string' => $this->attributes->query,
            'fragment'     => $this->attributes->fragment
        ];
    }
}
