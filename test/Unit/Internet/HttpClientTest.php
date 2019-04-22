<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\HttpClient;
use Nonetallt\Helpers\Internet\Http\HttpRequest;
use Nonetallt\Helpers\Internet\Http\HttpRequestCollection;
use Test\Unit\Internet\TestsHttpClient;

class HttpClientTest extends TestCase
{
    use TestsHttpClient;

    /**
     * @group remote
     */
    public function testSendRequestWorks()
    {
        $client = new HttpClient();
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 200]);
        $response = $client->sendRequest(new HttpRequest('GET', $url));
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @group remote
     */
    public function testSendRequestsWorks()
    {
        $client = new HttpClient();
        $requests = new HttpRequestCollection();
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 200]);
        $requests->push(new HttpRequest('GET', $url));
        $requests->push(new HttpRequest('GET', $url));
        $requests->push(new HttpRequest('GET', $url));

        $responses = $client->sendRequests($requests);
        $responseIsSuccessful = $responses->map(function($response) {
            return $response->isSuccessful();
        });

        $this->assertEquals([true, true, true], $responseIsSuccessful);
    }

    /**
     * @group remote
     */
    public function testSendRequestCreatesConnectionErrorWhenHostnameDoesNotExist()
    {
        $client = new HttpClient();
        $response = $client->sendRequest(new HttpRequest('GET', 'foobar'));
        $error = $response->getExceptions()->first()->getMessage();
        $this->assertEquals("Couldn't resolve host name (connection error code 6)", $error);
    }

    /**
     * @group remote
     */
    public function testSendRequestsCreatesConnectionErrorWhenHostnameDoesNotExist()
    {
        $client = new HttpClient();
        $requests = new HttpRequestCollection();
        $requests->push(new HttpRequest('GET', 'foobar'));
        $requests->push(new HttpRequest('GET', 'foobar'));
        $requests->push(new HttpRequest('GET', 'foobar'));

        $responses = $client->sendRequests($requests);
        $expected = [
            "Couldn't resolve host name (connection error code 6)",
            "Couldn't resolve host name (connection error code 6)",
            "Couldn't resolve host name (connection error code 6)",
        ];

        $this->assertEquals($expected, $responses->getExceptions()->getMessages());
    }

    /**
     * @group remote
     */
    public function testStatusCode500CountsAsError()
    {
        $client = new HttpClient();
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 500]);
        $response = $client->sendRequest(new HttpRequest('GET', $url));
        $expected = [
            'Server responded with code 500 (Internal Server Error)'
        ];
        $this->assertEquals($expected, $response->getErrors());
    }
}
