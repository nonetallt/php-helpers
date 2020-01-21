<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;

class CreateResponseExceptions
{
    private $errorAccessor;
    private $messageAccessor;
    private $factory;

    public function __construct(?string $errorAccessor, ?string $messageAccessor, ?HttpRequestResponseExceptionFactory $factory)
    {
        $this->errorAccessor   = $errorAccessor;
        $this->messageAccessor = $messageAccessor;
        $this->factory         = $factory ?? new HttpRequestResponseExceptionFactory();
    }

    public function createExceptions(HttpResponse $response)
    {
        $exceptions = new HttpRequestExceptionCollection();
        $body = $response->getBody();

        if($body !== null) {
            $body = $response->getBody()->getParsed();
        }

        if(is_array($body)) {
            $exceptionData = $this->accessResponseExceptions($body);

            /* If response exceptions were found, add them to the request */
            if(! empty($exceptionData)) {
                $exceptions = $this->factory->createExceptions($exceptionData);
            }
        }

        return $exceptions;
    }

    /**
     * Find exception data contained in the given response
     *
     * @param array $parsedResponseData Parsed request data that is being searched for errors
     *
     * @return mixed $exceptionData Exceptions that were found. Empty values
     * will not be processed by error handlers
     *
     */
    protected function accessResponseExceptions(array $parsed)
    {
        if($this->errorAccessor === null || $parsed === null) return;
        $accessor = new RecursiveAccessor('->');

        /* Not errors found */
        if(! $accessor->isset($this->errorAccessor, $parsed)) return;

        /* Try finding error objects from the response */
        $exceptionData = $accessor->getNestedValue($this->errorAccessor, $parsed);

        /* Try finding messages from within error objects */
        if($this->messageAccessor !== null) {
            $exceptionData = array_map(function($error) use ($accessor) {
                return $accessor->getNestedValue($this->messageAccessor, $error);
            }, $exceptionData);
        }

        return $exceptionData;
    }
}
