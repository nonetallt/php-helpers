<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\ParsedHttpResponse;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;

/**
 * A http client that parses responses
 */
abstract class ResponseParsingHttpClient extends HttpClient
{
    protected $errorAccessor;
    protected $errorMessageAccessor;
    private $responseExceptionFactory;

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
     * @override
     */
    protected function createResponse(HttpRequest $request, ?Response $response, ?RequestException $exception = null) : HttpResponse
    {
        /* Create connection exceptions for the request */
        $connectionExceptions = $this->createConnectionExceptions($exception);
        $responseWrapper = $this->createResponseClass($request, $response, $connectionExceptions);
        $exceptionData = $this->accessResponseExceptions($responseWrapper);

        /* If response exceptions were found, add them to the request */
        if(! empty($exceptionData)) {
            $factory = $this->getResponseExceptionFactory();
            $exceptions = $factory->createExceptions($exceptionData);
            $responseWrapper->getExceptions()->pushAll($exceptions);
        }

        return $responseWrapper;
    }

    /**
     * Find exception data contained in the given response
     *
     * @param Nonetallt\Helpers\Internet\Http\Requests\HttpRequest $request The
     * request that is being searched for errors
     *
     * @return mixed $exceptionData Exceptions that were found. Empty values
     * will not be processed by error handlers
     *
     */
    protected function accessResponseExceptions(ParsedHttpResponse $request)
    {
        if($this->errorAccessor === null) return;

        $accessor = new RecursiveAccessor('->');
        $parsed = $request->getParsed();

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

    private function getResponseExceptionFactory() : HttpRequestResponseExceptionFactory
    {
        if($this->responseExceptionFactory === null) {
            $this->responseExceptionFactory = $this->createResponseExceptionFactory();
        }

        return $this->responseExceptionFactory;
    }

    /**
     * Create the exception factory that should be used by this http client for
     * creating response exceptions.
     *
     * @return HttpRequsetResponseExceptionFactory $factory
     *
     */
    protected function createResponseExceptionFactory() : HttpRequestResponseExceptionFactory
    {
        return new HttpRequestResponseExceptionFactory();
    }

    abstract protected function createResponseClass(HttpRequest $request, ?Response $response, HttpRequestExceptionCollection $exceptions) : ParsedHttpResponse;
}
