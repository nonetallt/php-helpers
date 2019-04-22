<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\JsonHttpClient;
use Nonetallt\Helpers\Internet\Http\HttpRequest;
use Test\Unit\Internet\TestsHttpClient;
use Nonetallt\Helpers\Templating\RecursiveAccessor;

class JsonHttpClientTest extends TestCase
{
    use TestsHttpClient;

    /**
     * @group remote
     */
    public function testSendRequestWorks()
    {
        $client = new JsonHttpClient();
        $url = $this->router->parseUrl($this->config('http.json_url'));
        $response = $client->sendRequest(new HttpRequest('GET', $url));
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @group remote
     */
    public function testResponseIsParsed()
    {
        $client = new JsonHttpClient();
        $url = $this->router->parseUrl($this->config('http.json_url'));
        $response = $client->sendRequest(new HttpRequest('GET', $url));

        $accessor = new RecursiveAccessor('.');
        $this->assertTrue($accessor->isset('slideshow.title', $response->getParsed()));
    }
}
