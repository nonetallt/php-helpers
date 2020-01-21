<?php

namespace Test\Unit\Internet\Http;

use Nonetallt\Helpers\Filesystem\Json\JsonParser;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Routing\Router;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClass;

trait TestsHttpClient
{
    private $config;
    private $configAccessor;
    private $router;

    public function setUp()
    {
        $this->initializeRouter();
    }

    public function initializeRouter()
    {
        $ref = new ReflectionClass($this);
        $rootPath = dirname($ref->getPsr4NamespaceRoot());

        /* Load testing conf */
        $configPath =  "$rootPath/testing_config.json";
        $parser = new JsonParser();
        $this->config = $parser->decodeFile($configPath, true);
        $this->configAccessor = new RecursiveAccessor('.');
        $this->router = new Router('{$}');
    }

    private function config(string $option)
    {
        return $this->configAccessor->getNestedValue($option, $this->config);
    }
}

