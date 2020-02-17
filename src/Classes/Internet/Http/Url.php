<?php

namespace Nonetallt\Helpers\Internet\Http;

use Nonetallt\Helpers\Common\Settings;

class Url extends Settings
{
    protected static function defineSettings() : array
    {
        return [
            [
                'name' => 'scheme',
                'default' => 'http',
                'validate' => 'in:http,https'
            ],
            [
                'name' => 'host',
                'validate' => 'string'
            ],
            [
                'name' => 'port',
                'validate' => 'integer|min:1|max:65535'
            ],
            [
                'name' => 'user',
                'validate' => 'string'
            ],
            [
                'name' => 'pass',
                'validate' => 'string'
            ],
            [
                'name' => 'path',
                'validate' => 'string'
            ],
            [
                'name' => 'query',
                'validate' => 'string'
            ],
            [
                'name' => 'fragment',
                'validate' => 'string'
            ]
        ];
    }

    public function __toString()
    {
        $data = $this->toArray(true);
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
        return $this->scheme;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->pass;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQueryString()
    {
        return $this->query;
    }

    public function getQuery() : HttpQuery
    {
        return HttpQuery::fromString($this->query);
    }
}
