<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use GuzzleHttp\Psr7\Response;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Generic\Exceptions\ParsingException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestResponseException;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;

/**
 * HttpResponse which has it's body parsed to a certain format.
 */
abstract class ParsedHttpResponse extends HttpResponse
{
    private $parsed;

    /**
     * @param App\Domain\Api\HttpRequest $originalRequest Request that got this
     * response.
     *
     * @param GuzzleHttp\Psr7\Response $response can be null for unfulfilled
     * requests.
     *
     * @param Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection $exceptions
     * connection exceptions.
     */
    public function __construct(HttpRequest $originalRequest, ?Response $response = null, HttpRequestExceptionCollection $exceptions)
    {
        parent::__construct($originalRequest, $response, $exceptions);

        try {
            /* Only parse if there are no connection exceptions */
            if($exceptions->isEmpty()) $this->parseBody($this->getBody());
        }
        catch(ParsingException $e) {
            $msg = "Response could not be parsed";
            $this->exceptions->push(new HttpRequestResponseException($msg, 0, $e));
        }
    }

    /**
     * Try finding errors in the parsed response by using these keys and
     * create an exception for each errors.
     *
     * @param string $errorAccessor Accessor path, '->' is used to access
     * nested values.
     *
     * @param string $messageAccessor Message accessor path, determines which
     * key should be used for the created exception messages defaults to
     * errorAccessor if null
     *
     */
    public function createResponseExceptions(string $errorAccessor, ?string $messageAccessor = null, string $nestedAccessorFormat = '->')
    {
        /* Do not attempt to use response if there is no body to parse */
        if(! $this->hasBody()) return;

        $accessor = new RecursiveAccessor($nestedAccessorFormat);
        $parsed = $this->getParsed();

        /* Not errors found */
        if(! $accessor->isset($errorAccessor, $parsed)) return;

        /* Try finding error objects from the response */
        $errorMessages = $accessor->getNestedValue($errorAccessor, $parsed);

        /* Try finding messages from within error objects */
        if($messageAccessor !== null) {
            $errorMessages = array_map(function($error) use ($accessor, $messageAccessor) {
                return $accessor->getNestedValue($messageAccessor, $error);
            }, $errorMessages);
        }

        if(is_string($errorMessages)) {
            $this->exceptions->push(new HttpRequestResponseException($errorMessages));
        }

        if(is_array($errorMessages)) {
            foreach($errorMessages as $message) {
                $this->exceptions->push(new HttpRequestResponseException($message));
            }
        }
    }

    /**
     * Get the parsed response body
     *
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */
    public function getParsed()
    {
        if($this->parsed === null) {
            $body = $this->getBody();
            if($body !== '') $this->parsed = $this->parseBody($body);
        }

        return $this->parsed;
    }

    /**
     * Parse the response body
     *
     * @throws Nonetallt\Helpers\Generic\Exceptions\ParsingException
     *
     * @return mixed $parsed Parsed response
     */
    abstract protected function parseBody(string $body);
}
