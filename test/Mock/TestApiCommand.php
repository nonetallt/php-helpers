<?php

namespace Test\Mock;

use Nonetallt\Helpers\Internet\Http\Api\BaseHttpApiCommand;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;

class TestApiCommand extends BaseHttpApiCommand
{ 
    private $method;
    private $url;

    public function __construct(string $method, string $url, array $query = [])
    {
        $this->method = $method;
        $this->url = $url;
        $this->query = $query;
    }

    public function getRequest() : HttpRequest
    {
        return new HttpRequest($this->method, $this->url, $this->query);
    }
}
