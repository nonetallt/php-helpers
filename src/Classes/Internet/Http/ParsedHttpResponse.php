<?php

namespace Nonetallt\Helpers\Internet\Http;

use GuzzleHttp\Psr7\Response;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Generic\Exceptions\ParsingException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;

abstract class ParsedHttpResponse extends HttpResponse
{
    private $parsed;
    private $errorAccessor;
    private $errorMessageAccessor;

    /**
     * @param App\Domain\Api\HttpRequest $originalRequest Request that got this
     * response.
     *
     * @param GuzzleHttp\Psr7\Response $response can be null for unfulfilled
     * requests.
     */
    public function __construct(HttpRequest $originalRequest, ?Response $response = null)
    {
        parent::__construct($originalRequest, $response);

        try {
            $this->parseBody($this->getBody());
        }
        catch(ParsingException $e) {
            $msg = "Request response could not be parsed";
            $this->exceptions->push(new HttpRequestResponseException($msg, 0, $e));
        }
    }

    /**
     * Try finding errors in the parsed response json by using these keys and
     * create exceptions for these errors.
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
        $this->errorAccessor = $error;
        $this->errorMessageAccessor = $message;
    }

    /**
     * @override
     */
    public function getExceptions() : HttpRequestExceptionCollection
    {
        $accessor = new RecursiveAccessor('->');

        /* Do not attempt to parse response if it has errors already */
        if($this->hasErrors()) return $this->errors;
        if($this->errorAccessor === null) return $this->errors;
        if(! $accessor->isset($this->errorAccessor, $this->getDecoded())) return $this->errors;

        /* For example, error can have attribute 'message' */
        $errorMessages = $accessor->getNestedValue($this->errorAccessor, $this->getDecoded());

        if(! is_null($this->errorMessageAccessor)) {
            $errorMessages = array_map(function($error) use($accessor){
                return $accessor->getNestedValue($this->errorMessageAccessor, $error);
            }, $errorMessages);
        }

        return $this->errors->merge($this->createExceptions($errorMessages));
    }

    /**
     * Create exceptions from parsed response content if applicable
     */
    private function createExceptions($error) : HttpResponseExceptionCollection
    {
        $exceptions = new HttpRequestExceptionCollection();

        if(is_string($error)) {
            $exceptions->push(new HttpRequestException($error));
            return $exceptions;
        }

        if(is_array($error)) {
            foreach($error as $message) {
                $exceptions->push(new HttpRequestException($message));
            }
            return $exceptions;
        }

        /* No exceptions are created if error is not string or array */
        return $exceptions;
    }

    /**
     * @throws Nonetallt\Helpers\Generic\Exceptions\ParsingException
     *
     * @return array $parsed Parsed response
     */
    protected abstract function parseBody(string $body) : array;

    /**
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */
    public function getParsed()
    {
        if($this->parsed === null) {
            $this->parsed = $this->parseBody($this->getBody());
        }

        return $this->parsed;
    }
}
