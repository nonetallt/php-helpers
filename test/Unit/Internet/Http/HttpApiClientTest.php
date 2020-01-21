<?php

namespace Test\Unit\Internet\Http;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Clients\HttpApiClient;
use Nonetallt\Helpers\Internet\Http\Api\Exceptions\ApiCommandNotFoundException;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClass;

class HttpApiClientTest extends TestCase
{
    use TestsHttpClient;

    private $client;

    public function setUp() : void
    {
        parent::setUp();
        $this->initializeRouter();
        $this->client = new HttpApiClient();
    }

    public function testCallingFakeMethodThrowsCommandNotFoundException()
    {
        $this->expectException(ApiCommandNotFoundException::class);
        $this->client->foobar();
    }

    public function testTestCommandCanBeLoadedAndCalled()
    {
        $ref = new ReflectionClass($this);
        $dir = $ref->getPsr4NamespaceRoot() . '/Mock';
        $this->client->loadApiCommands($dir, 'Test\\Mock');

        $response = $this->client->testApiCommand('POST', $this->config('http.echo_url'), [
            'data' => 'test'
        ]);

        $this->assertEquals('test', $response->getBody());
    }
}
