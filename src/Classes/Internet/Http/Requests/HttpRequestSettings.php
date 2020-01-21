<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\ResponseParser;
use Nonetallt\Helpers\Common\Settings;

class HttpRequestSettings extends Settings
{
    const DEFAULTS = [
        'ignored_error_codes'        => [],
        /* 'error_accessor'             => null, */
        /* 'error_message_accessor'    => null, */
        /* 'response_exception_factory' => null, */
        /* 'response_parser'            => null */
    ];

    public function __construct(array $options = [])
    {
        $parserClass = ResponseParser::class;
        $factoryClass = HttpRequestResponseExceptionFactory::class;

        $validators = [
            'ignored_error_codes'        => 'array',
            'error_accessor'             => 'string',
            'error_message_accessor'     => 'string',
            'response_parser'            => "is:$parserClass",
            'response_exception_factory' => "is:$factoryClass",
        ];

        $whitelisted = array_keys($validators);

        parent::__construct($options, static::DEFAULTS, $validators, $whitelisted);

        $this->exceptionFactory = new HttpRequestResponseExceptionFactory();
    }

    public function ignoreErrorCodes(array $codes)
    {
        foreach($codes as $code) {
            $this->ignoreErrorCode($code);
        }
    }

    public function ignoreErrorCode(int $code, bool $ignore = true)
    {
        if(400 > $code || $code > 600) {
            $msg = "Error codes can only be ignored from 4XX - 5XX range, $code given";
            throw new \InvalidArgumentException($msg);
        }

        $this->ignored_error_codes[$code] = $ignore;
    }

    public function isErrorCodeIgnored(int $code) : bool
    {
        return in_array($code, array_keys($this->ignored_error_codes));
    }

    /**
     * Use keys in the parsed json to create exceptions for requests when those
     * keys exist. For example, if accessor is set as 'request->error', 
     * then the existance of property 'error' of 'request' is checked to see wether there should be an error.
     *
     * @param string $errorAccessor Accessor path, '->' is used to access
     * nested values.
     *
     * @param string $messageAccessor Message accessor path, determines which
     * key should be used for the created exception messages defaults to
     * errorAccessor if null
     *
     */
    public function setErrorAccessors(?string $errorAccessor, ?string $messageAccessor)
    {
        $this->error_accessor = $errorAccessor;
        $this->error_message_accessor = $messageAccessor;
    }
}

