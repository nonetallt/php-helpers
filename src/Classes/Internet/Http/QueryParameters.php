<?php

namespace Nonetallt\Helpers\Internet\Http;

class QueryParameters
{
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
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

    public function toArray()
    {
        return $this->parameters;
    }
}
