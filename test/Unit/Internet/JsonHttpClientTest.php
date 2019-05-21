<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Clients\JsonHttpClient;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Test\Unit\Internet\TestsHttpClient;
use Nonetallt\Helpers\Templating\RecursiveAccessor;

class JsonHttpClientTest extends TestCase
{
    use TestsHttpClient;

    private $client;

    public function setUp()
    {
        $this->initializeRouter();
        $this->client = new JsonHttpClient();
    }

    /**
     * @group remote
     */
    public function testSendRequestWorks()
    {
        $url = $this->router->parseUrl($this->config('http.json_url'));
        $response = $this->client->sendRequest(new HttpRequest('GET', $url));
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @group remote
     */
    public function testResponseIsParsed()
    {
        $url = $this->router->parseUrl($this->config('http.json_url'));
        $response = $this->client->sendRequest(new HttpRequest('GET', $url));

        $accessor = new RecursiveAccessor('.');
        $this->assertTrue($accessor->isset('slideshow.title', $response->getParsed()));
    }

    /**
     * @group remote
     */
    public function testExceptionsAreCreatedWhenWhenErrorAccessorIsSet()
    {
        $this->client->setErrorAccessors('slideshow->slides', 'title');
        $url = $this->router->parseUrl($this->config('http.json_url'));
        $response = $this->client->sendRequest(new HttpRequest('GET', $url));

        $expected = [
            'Wake up to WonderWidgets!',
            'Overview'
        ];

        $this->assertEquals($expected, $response->getErrors());
    }

    /**
     * @group remote
     */
    public function testExceptionIsCreatedIfParsingFails()
    {
        $data = json_encode([
            'errors' => [
                ['message' => 'error_1'],
                ['message' => 'error_2']
            ]
        ]);

        $request = new HttpRequest('POST', $this->config('http.echo_url'), [
            'data' => substr($data, 15)
        ]);

        $response = $this->client->sendRequest($request);
        $expected = ['Response could not be parsed'];

        $this->assertEquals($expected, $response->getErrors());
    }

    /**
     * @group remote
     */
    public function testExceptionIsCretedWhenResponseHas404StausCode()
    {
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 404]);
        $request = new HttpRequest('GET', $url);

        $response = $this->client->sendRequest($request);
        $expected = ['Server responded with code 404 (Not Found)'];

        $this->assertEquals($expected, $response->getErrors());
    }
}
