<?php

namespace Nonetallt\Helpers\Internet\Http\Common;

use Nonetallt\Helpers\Generic\Collection;

class HttpHeaderCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, HttpHeader::class);
    }

    public static function fromArray(array $array) : self
    {
        $collection = new self();

        foreach($array as $name => $value) {
            $collection->push(new HttpHeader($name, $value));
        }
        
        return $collection;
    }

    public function setAuthorization($auth)
    {
        /* TODO create request processor that sets auth */
        if(is_string($auth)) {
            $this->pushOrReplace(new HttpHeader('Authorization', $auth));
            return;
        }

        if(is_array($auth)) {
            if(! isset($auth[0])) {
                $msg = "Auth array must contain 0 index used as the username";
                throw new \InvalidArgumentException($msg);
            }

            if(! isset($auth[1])) {
                $msg = "Auth array must contain 1 index used as the password";
                throw new \InvalidArgumentException($msg);
            }

            $value = 'Basic ' . base64_encode("{$auth[0]}:{$auth[1]}");
            $this->pushOrReplace(new HttpHeader('Authorization', $value));
            return;
        }
         
        $given = (new DescribeObject($auth))->describeType();
        $msg = "Auth must be either a string or an array, $given given";
        throw new \InvalidArgumentException($msg);
    }

    public function pushOrReplace(HttpHeader $header)
    {
        $headerExists = false;

        foreach($this->items as $index => $item) {
            if($item->getName() === $header->getName()) {
                $this->items[$index] = $header;
                $headerExists = true;
                break;
            }
        }

        if(! $headerExists) {
            $this->push($header);
        }
    }

    public function toArray() : array
    {
        return $this->map(function($item) {
            return $this->item;
        });
    }
}
