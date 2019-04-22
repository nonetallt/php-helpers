<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Internet\Http\HttpClient;
use Nonetallt\Helpers\Internet\Http\HttpRequest;
use Nonetallt\Helpers\Internet\Http\HttpRequestCollection;
use Nonetallt\Helpers\Filesystem\Json\JsonParser;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Routing\Router;

class HttpClientTest extends TestCase
{
    private $config;
    private $configAccessor;
    private $router;

    public function setUp()
    {
        /* Load testing conf */
        $configPath = dirname(dirname(dirname(__DIR__))) . '/testing_config.json';
        $parser = new JsonParser();
        $this->config = $parser->decodeFile($configPath, true);
        $this->configAccessor = new RecursiveAccessor('.');
        $this->router = new Router('{$}');
    }

    private function config(string $option)
    {
        return $this->configAccessor->getNestedValue($option, $this->config);
    }

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
