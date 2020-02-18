<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Internet\Http\Exceptions\Factory\HttpRequestResponseExceptionFactory;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestResponseException;
use Nonetallt\Helpers\Generic\Exceptions\ParsingException;
use Nonetallt\Helpers\Templating\RecursiveAccessor;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponseHandler;

/**
 * Processor that creates exceptions for response data that can't
 * be parsed
 *
 */
class CreateResponseExceptions implements HttpResponseProcessor
{
    public function process(HttpResponse $response, HttpResponseHandler $handler)  : HttpResponse
    {
        $response->getExceptions()->pushAll($this->createExceptions($response));
        return $response;
    }

    private function createExceptions(HttpResponse $response)
    {
        $exceptions = new HttpRequestExceptionCollection();
        $body = $response->getBody();

        if($body === null) {
            return $exceptions;
        }

        try {
            $body = $body->getParsed();
        }
        catch(ParsingException $e) {
            $class = get_class($response->getRequest()->getSettings()->response_parser);
            $msg = "Response could not be parsed using $class";
            $exceptions->push(new HttpRequestResponseException($msg, 0, $e));
        }
        
        if(is_array($body)) {
            $exceptionData = $this->accessResponseExceptions($response, $body);

            /* If response exceptions were found, add them to the request */
            if(! empty($exceptionData)) {
                $factory = $response->getRequest()->getSettings()->response_exception_factory;
                $exceptions = $factory->createExceptions($exceptionData);
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
    private function accessResponseExceptions(HttpResponse $response, array $parsed)
    {
        $settings = $response->getRequest()->getSettings();
        $errorAccessor = $settings->error_accessor;
        $messageAccessor = $settings->error_message_accessor;

        if($errorAccessor === null || $parsed === null) return;
        $accessor = new RecursiveAccessor('->');

        /* Not errors found */
        if(! $accessor->isset($errorAccessor, $parsed)) return;

        /* Try finding error objects from the response */
        $exceptionData = $accessor->getNestedValue($errorAccessor, $parsed);

        /* Try finding messages from within error objects */
        if($messageAccessor !== null) {
            $exceptionData = array_map(function($error) use ($accessor, $messageAccessor) {
                return $accessor->getNestedValue($messageAccessor, $error);
            }, $exceptionData);
        }

        return $exceptionData;
    }
}
