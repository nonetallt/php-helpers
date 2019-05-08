<?php

namespace Nonetallt\Helpers\Internet\Http\Clients;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Responses\ParsedHttpResponse;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;

/**
 * A http client that parses responses
 */
abstract class ResponseParsingHttpClient extends HttpClient
{
    protected $errorAccessor;
    protected $errorMessageAccessor;

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
        $responseWrapper = $this->createResponseClass($request, $response, $this->createConnectionExceptions($exception));

        if($this->errorAccessor !== null) {
            $responseWrapper->createResponseExceptions($this->errorAccessor, $this->errorMessageAccessor);
        }

        return $responseWrapper;
    }

    abstract protected function createResponseClass(HttpRequest $request, ?Response $response, HttpRequestExceptionCollection $exceptions) : ParsedHttpResponse;
}
