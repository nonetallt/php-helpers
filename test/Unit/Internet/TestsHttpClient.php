<?php

namespace Test\Unit\Internet;

use Nonetallt\Helpers\Filesystem\Json\JsonParser;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Routing\Router;

trait TestsHttpClient
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
}

