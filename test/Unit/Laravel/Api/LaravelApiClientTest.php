<?php

namespace Test\Unit\Laravel\Api;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Test\Unit\Internet\TestsHttpClient;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Laravel\Api\LaravelApiClient;

class LaravelApiClientTest extends TestCase
{
    use TestsHttpClient;

    private $client;

    public function setUp() : void
    {
        $this->initializeRouter();
        $this->client = new LaravelApiClient();
    }

    /**
     * @group remote
     */
    public function testErrorMessagesAreParsedCorrectly()
    {
        $url = $this->router->parseUrl($this->config('http.echo_url'));

        $responseData = [
            'errors' => [
                'username' => [
                    'The username field is required.',
                    'The username must be at least 5 characters.'
                ],
                'password' => [
                    'The password field is required.',
                    'The password must be at least 8 characters.'
                ]
            ]
        ];

        $response = $this->client->sendRequest(new HttpRequest('POST', $url, [
            'data' => json_encode($responseData)
        ]));

        $expected = array_merge($responseData['errors']['username'], $responseData['errors']['password']);
        $this->assertEquals($expected, $response->getErrors());
    }
}
