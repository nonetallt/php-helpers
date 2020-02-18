<?php

namespace Nonetallt\Helpers\Internet\Http\Requests;

use Nonetallt\Helpers\Common\Settings;
use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\ResponseParser;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\HttpResponseProcessorCollection;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\CreateConnectionExceptions;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\CreateResponseExceptions;

class HttpRequestSettings extends Settings
{
    protected static function defineSettings() : array
    {
        $factoryClass = HttpRequestResponseExceptionFactory::class;
        $parserClass = ResponseParser::class;
        $processorCollectionClass = HttpResponseProcessorCollection::class;

        return [
            [
                'name' => 'timeout',
                'default' => 10.0,
                'validate' => 'float|min:0'
            ],
            [
                'name' => 'ignored_error_codes',
                'default' => [],
                'validate_items' => 'integer|min:400|max:599'
            ],
            [
                'name' => 'error_accessor',
                'default' => null,
                'validate' => 'string|min:1'
            ],
            [
                'name' => 'error_message_accessor',
                'default' => null,
                'validate' => 'string|min:1'
            ],
            [
                'name' => 'response_parser',
                'default' => null,
                'validate' => "is:$parserClass"
            ],
            [
                'name' => 'response_exception_factory',
                'default' => function() {
                    return new HttpRequestResponseExceptionFactory();
                },
                'validate' => "is:$factoryClass"
            ],
            [
                'name' => 'request_processors',
                'default' => function() {
                    return new HttpResponseProcessorCollection(
                        new CreateConnectionExceptions(),
                        new CreateResponseExceptions()
                    );
                },
                'validate' => "is:$processorCollectionClass"
            ]
        ];
    }

    public function isErrorCodeIgnored(int $code) : bool
    {
        return in_array($code, $this->ignored_error_codes);
    }
}

