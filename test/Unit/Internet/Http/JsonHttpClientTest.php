<?php

namespace Test\Unit\Internet\Http;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Http\Clients\JsonHttpClient;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\JsonResponseParser;

class JsonHttpClientTest extends TestCase
{
    use TestsHttpClient;

    private $client;

    public function setUp() : void
    {
        $this->initializeRouter();
        $this->client = new JsonHttpClient();
    }

    /**
     * @group remote
     */
    public function testSendRequestWorks()
    {
        $url = $this->router->parseUrl($this->config('http.echo_url'));
        $response = $this->client->sendRequest(new HttpRequest('POST', $url, [
            'data' => json_encode(['foo' => 'bar'])
        ]));
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @group remote
     */
    public function testResponseIsParsed()
    {
        $url = $this->router->parseUrl($this->config('http.json_url'));
        $request = new HttpRequest('GET', $url);
        $response = $this->client->sendRequest($request);

        $accessor = new RecursiveAccessor('.');
        $this->assertTrue($accessor->isset('slideshow.title', $response->getBody()->getParsed()));
    }

    /**
     * @group remote
     */
    public function testExceptionsAreCreatedWhenWhenErrorAccessorIsSet()
    {
        $url = $this->router->parseUrl($this->config('http.json_url'));
        $request = new HttpRequest('GET', $url);

        $request->getSettings()->setAll([
            'error_accessor'         => 'slideshow->slides',
            'error_message_accessor' => 'title'
        ]);

        $response = $this->client->sendRequest($request);

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
        $class = JsonResponseParser::class;
        $expected = ["Response could not be parsed using $class"];

        $this->assertEquals($expected, $response->getErrors());
    }

    /**
     * @group remote
     */
    public function testExceptionIsCreatedWhenResponseHas404StatusCode()
    {
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 404]);
        $request = new HttpRequest('GET', $url);

        $response = $this->client->sendRequest($request);
        $class = JsonResponseParser::class;

        $expected = [
            'Server responded with code 404 (Not Found)',
            "Response could not be parsed using $class"
        ];

        $this->assertEquals($expected, $response->getErrors());
    }
}
