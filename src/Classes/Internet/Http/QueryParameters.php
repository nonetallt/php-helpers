<?php

namespace Nonetallt\Helpers\Internet\Http;

class QueryParameters implements \ArrayAccess
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

    public function toArray() : array
    {
        return $this->parameters;
    }

    // ArrayAccess methods
    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) {
            $this->parameters[] = $value;
        } else {
            $this->parameters[$offset] = $value;
        }
    }

    public function offsetExists($offset) 
    {
        return isset($this->parameters[$offset]);
    }

    public function offsetUnset($offset) 
    {
        unset($this->parameters[$offset]);
    }

    public function offsetGet($offset) 
    {
        return isset($this->parameters[$offset]) ? $this->parameters[$offset] : null;
    }
}
