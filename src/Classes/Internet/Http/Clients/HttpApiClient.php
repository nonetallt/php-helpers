<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use Nonetallt\Helpers\Internet\Http\Api\HttpApiCommandFactory;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Filesystem\Reflections\Exceptions\AliasNotFoundException;
use Nonetallt\Helpers\Internet\Http\Api\Exceptions\ApiCommandNotFoundException;
use Nonetallt\Helpers\Internet\Http\Api\HttpApiCommand;

class HttpApiClient
{
    private $client;
    private $factory;
    private $processors;

    public function __construct()
    {
        $this->client = new HttpClient();
        $this->factory = new HttpApiCommandFactory();
    }

    /**
     * Add commands for this api client from 
     *
     */
    public function loadApiCommands(string $dir = null, string $namespace = null)
    {
        $dir = $dir ?? __DIR__;
        $namespace = $namespace ?? __NAMESPACE__;
        $this->factory->loadReflections($dir, $namespace);
    }

    /**
     * Get an api command instance with the given name and supplied parameters
     *
     */
    public function getApiCommand(string $name, array $params) : HttpApiCommand
    {
        try {
            $command = $this->factory->make($name, ...$params);
            return $command;
        }
        catch(AliasNotFoundException $e) {
            $msg = "Command '$name' could not be found";
            throw new ApiCommandNotFoundException($msg, 0, $e);
        }
    }

    /**
     * Proxy method calls to api commands that are executed
     *
     */
    public function __call(string $method, array $params) : HttpResponse
    {
        $command = $this->getApiCommand($method, $params);
        $response = $this->client->sendRequest($command->getRequest());

        return $command->getResponse($response);
    }
}
