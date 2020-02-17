<?php

namespace Test\Unit\Internet\Http;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\HttpQuery;
use Nonetallt\Helpers\Internet\Http\Clients\HttpClient;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequestCollection;
use Nonetallt\Helpers\Internet\Http\Common\HttpHeader;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\CreateConnectionExceptions;

class HttpClientTest extends TestCase
{
    use TestsHttpClient;

    private $client;

    public function setUp() : void
    {
        parent::setUp();
        $this->initializeRouter();
        $this->client = new HttpClient();
    }

    /**
     * @group remote
     */
    public function testSendRequestWorks()
    {
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 200]);
        $response = $this->client->sendRequest(new HttpRequest('GET', $url));
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @group remote
     */
    public function testSendRequestsWorks()
    {
        $requests = new HttpRequestCollection();
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 200]);
        $requests->push(new HttpRequest('GET', $url));
        $requests->push(new HttpRequest('GET', $url));
        $requests->push(new HttpRequest('GET', $url));

        $responses = $this->client->sendRequests($requests);
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
        $response = $this->client->sendRequest(new HttpRequest('GET', 'foobar'));
        $error = $response->getExceptions()->first()->getMessage();
        $this->assertEquals("Couldn't resolve host name (connection error code 6)", $error);
    }

    /**
     * @group remote
     */
    public function testSendRequestsCreatesConnectionErrorWhenHostnameDoesNotExist()
    {
        $requests = new HttpRequestCollection();
        $requests->push(new HttpRequest('GET', 'foobar'));
        $requests->push(new HttpRequest('GET', 'foobar'));
        $requests->push(new HttpRequest('GET', 'foobar'));

        $responses = $this->client->sendRequests($requests);
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
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 500]);
        $response = $this->client->sendRequest(new HttpRequest('GET', $url));
        $expected = [
            'Server responded with code 500 (Internal Server Error)'
        ];
        $this->assertEquals($expected, $response->getErrors());
    }

    /**
     * @group remote
     */
    public function testRedirectionTraceIsSaved()
    {
        $url = $this->router->parseUrl($this->config('http.redirect_url'));
        $destination = 'https://www.google.com';
        $query = new HttpQuery([
            'times' => 3,
            'destination' => $destination
        ]);

        $request = new HttpRequest('POST', $url, $query->toArray());
        $response = $this->client->sendRequest($request);
        $trace = $response->getRequest()->getRedirections()->getUrlTrace();

        $firstUrl = $url . (string)(new HttpQuery(['times' => 2, 'destination' => $destination]));
        $secondUrl = $url . (string)(new HttpQuery(['times' => 1, 'destination' => $destination]));

        $expected = [
            $firstUrl,
            $secondUrl,
            $destination
        ];

        $this->assertEquals($expected, $trace);
    }

    /**
     * @group remote
     */
    public function testAuthorizationHeaderIsSetUsingBasicAuthWhenAuthIsSetUsingArray()
    {
        $auth = ['username', 'password'];
        
        $url = $this->router->parseUrl($this->config('http.header_url'));
        $request = new HttpRequest('POST', $url, ['header' => 'Authorization']);
        $request->getHeaders()->setAuthorization($auth);

        $response = $this->client->sendRequest($request);
        $encoded = base64_encode($auth[0].':'.$auth[1]);
        $expected = "Basic $encoded";

        $decodedResponse = json_decode($response->getBody(), true);
        $this->assertEquals($expected, $decodedResponse['Authorization']);
    }

    /**
     * @group remote
     */
    public function testAuthorizationHeaderIsSetWhenAuthIsSetUsingString()
    {
        $auth = 'token';
        $url = $this->router->parseUrl($this->config('http.header_url'));
        $request = new HttpRequest('POST', $url, ['header' => 'Authorization']);
        $request->getHeaders()->setAuthorization($auth);

        $response = $this->client->sendRequest($request);
        $expected = ['Authorization' => $auth];

        $this->assertEquals($expected, json_decode($response->getBody(), true));
    }

    /**
     * @group remote
     */
    public function testResponseBodyCanBeFoundWhenErrorWas4xx()
    {
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 400]);
        $request = new HttpRequest('GET', $url);
        $response = $this->client->sendRequest($request);

        $this->assertEquals('successful', $response->getBody());
    }

    /**
     * @group remote
     */
    public function test4xxErrorsCanBeIgnored()
    {
        $url = $this->router->parseUrl($this->config('http.status_code_url'), ['code' => 400]);
        $request = new HttpRequest('GET', $url);
        $request->getSettings()->ignored_error_codes += [400];
        $response = $this->client->sendRequest($request);

        $this->assertEmpty($response->getErrors());
    }

    /**
     * @group remote
     */
    public function testCustomHeadersCanBeSetForEachRequest()
    {
        $headerValue = 'foobar';

        $url = $this->router->parseUrl($this->config('http.header_url'));
        $request = new HttpRequest('POST', $url, ['header' => 'custom']);
        $request->getHeaders()->push(new HttpHeader('custom', $headerValue));

        $response = $this->client->sendRequest($request);
        $expected = ['custom' => $headerValue];

        $this->assertEquals($expected, json_decode($response->getBody(), true));
    }

    public function testClientCanBeSerialized()
    {
        $serialized = serialize($this->client);
        $this->assertEquals($this->client, unserialize($serialized));
    }
}
