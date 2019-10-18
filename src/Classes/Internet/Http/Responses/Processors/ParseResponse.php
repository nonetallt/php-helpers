<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Generic\Traits\LazyLoadsProperties;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use GuzzleHttp\Exception\RequestException;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;
use Nonetallt\Helpers\Generic\Exceptions\ParsingException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestResponseException;

abstract class ParseResponse implements HttpResponseProcessor
{
    use LazyLoadsProperties;

    protected $errorAccessor;
    protected $errorMessageAccessor;

    /**
     * @throws Nonetallt\Helpers\Generic\Exceptions\ParsingException
     *
     */
    abstract protected function parseResponseBody(string $body) : array;


    public function processHttpResponse(HttpResponse $response, ?RequestException $previous = null) : HttpResponse
    {
        try {
            $parsedBody = $this->parseResponseBody($response->getBody());
            $exceptionData = $this->accessResponseExceptions($parsedBody);

            /* If response exceptions were found, add them to the request */
            if(! empty($exceptionData)) {
                $factory = $this->getResponseExceptionFactory();
                $exceptions = $factory->createExceptions($exceptionData);
                $response->getExceptions()->pushAll($exceptions);
            }
        }
        catch(ParsingException $e) {
            $msg = "Response could not be parsed";
            $response->exceptions->push(new HttpRequestResponseException($msg, 0, $e));
        }

        return $response;
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
    public function setErrorAccessors(string $errorAccessor, ?string $messageAccessor = null)
    {
        $this->errorAccessor = $errorAccessor;
        $this->errorMessageAccessor = $messageAccessor;
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
        if($this->errorAccessor === null) return;

        $accessor = new RecursiveAccessor('->');

        if($parsed === null) return;

        /* Not errors found */
        if(! $accessor->isset($this->errorAccessor, $parsed)) return;

        /* Try finding error objects from the response */
        $exceptionData = $accessor->getNestedValue($this->errorAccessor, $parsed);

        /* Try finding messages from within error objects */
        if($this->errorMessageAccessor !== null) {
            $exceptionData = array_map(function($error) use ($accessor) {
                return $accessor->getNestedValue($this->errorMessageAccessor, $error);
            }, $exceptionData);
        }

        return $exceptionData;
    }

    /**
     * Create the exception factory that should be used by this http client for
     * creating response exceptions.
     *
     * @return HttpRequsetResponseExceptionFactory $factory
     *
     */
    protected function lazyLoadResponseExceptionFactory() : HttpRequestResponseExceptionFactory
    {
        return new HttpRequestResponseExceptionFactory();
    }
}
