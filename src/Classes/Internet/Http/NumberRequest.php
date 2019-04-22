<?php

namespace App\Domain\Api;

use App\DidNumber;

class NumberRequest extends HttpRequest
{
    private $number;

    public function __construct(DidNumber $number, string $method, string $url, array $query = [])
    {
        $this->number = $number;
        parent::__construct($method, $url, $query);
    }

    public function getNumber()
    {
        return $this->number;
    }
}
