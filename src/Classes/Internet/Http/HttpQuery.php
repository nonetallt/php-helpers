<?php

namespace Nonetallt\Helpers\Internet\Http;

use Nonetallt\Helpers\Generic\Container;

class HttpQuery extends Container
{
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);
    }

    public static function fromString($value)
    {
        /* Empty value */
        if(! is_string($value) || $value === '') return new self([]);

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
        foreach($this->options as $key => $value) {
            $key = urlencode($key);
            $value = urlencode($value);
            $leader = $string === '' ? '?' : '&';
            $string .= "$leader$key=$value"; 
        }

        return $string;
    }
}
