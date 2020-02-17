<?php

namespace Test\Unit\Internet\Http;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Http\Clients\XmlHttpClient;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\XmlResponseParser;

class XmlClientTest extends TestCase
{
    use TestsHttpClient;

    private $client;

    public function setUp() : void
    {
        $this->initializeRouter();
        $this->client = new XmlHttpClient();
    }

    /**
     * @group remote
     */
    public function testSendRequestWorks()
    {
        $url = $this->router->parseUrl($this->config('http.xml_url'));
        $response = $this->client->sendRequest(new HttpRequest('GET', $url));
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @group remote
     */
    public function testResponseIsParsed()
    {
        $url = $this->router->parseUrl($this->config('http.xml_url'));
        $response = $this->client->sendRequest(new HttpRequest('GET', $url));

        $accessor = new RecursiveAccessor('.');
        $this->assertEquals('Sample Slide Show', $accessor->getNestedValue('@attributes.title', $response->getBody()->getParsed()));
    }

    /**
     * @group remote
     */
    public function testExceptionsAreCreatedWhenWhenErrorAccessorIsSet()
    {
        $url = $this->router->parseUrl($this->config('http.xml_url'));

        $request = new HttpRequest('GET', $url);
        $request->getSettings()->setAll([
            'error_accessor' => 'slide',
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
        $request = new HttpRequest('GET', $this->config('http.json_url'));
        $response = $this->client->sendRequest($request);
        $class = XmlResponseParser::class;
        $expected = ["Response could not be parsed using $class"];

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
        $class = XmlResponseParser::class;

        $expected = [
            'Server responded with code 404 (Not Found)',
            "Response could not be parsed using $class"
        ];

        $this->assertEquals($expected, $response->getErrors());
    }
}
