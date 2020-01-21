<?php

namespace Test\Unit\Internet\Http;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Clients\XmlHttpClient;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Templating\RecursiveAccessor;

class XmlClientTest extends TestCase
{
    use TestsHttpClient;

    private $client;

    public function setUp()
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
        $this->assertEquals('Sample Slide Show', $accessor->getNestedValue('@attributes.title', $response->getParsed()));
    }

    /**
     * @group remote
     */
    public function testExceptionsAreCreatedWhenWhenErrorAccessorIsSet()
    {
        $this->client->setErrorAccessors('slide', 'title');
        $url = $this->router->parseUrl($this->config('http.xml_url'));
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
        $request = new HttpRequest('GET', $this->config('http.json_url'));
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
