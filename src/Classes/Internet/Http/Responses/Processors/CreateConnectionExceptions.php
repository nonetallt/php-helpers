<?php

namespace Nonetallt\Helpers\Internet\Http\Responses\Processors;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestConnectionException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestServerException;
use Nonetallt\Helpers\Internet\Http\Responses\HttpResponse;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatusRepository;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestException;

class CreateConnectionExceptions implements HttpResponseProcessor
{
    private $ignoredErrorCodes = [];
    
    public function processHttpResponse(HttpResponse $response, ?RequestException $previous = null) : HttpResponse
    {
        if($previous !== null) {
            $exception = $this->getConnectionException($response->getRequest(), $previous);

            if($exception !== null) {
                $response->getExceptions()->push($exception);
            }
        }

        return $response;
    }

    private function getConnectionException(HttpRequest $request, RequestException $previous) : ?HttpRequestException
    {
        $curlErrorCode = $previous->getHandlerContext()['errno'] ?? 0;

        /* Handle curl errors if applicable */
        if($curlErrorCode !== 0) {
            $curlErrorMessage = curl_strerror($curlErrorCode);
            $msg = "$curlErrorMessage (connection error code $curlErrorCode)";
            return new HttpRequestConnectionException($msg, $curlErrorCode, $previous);
        }

        if($previous instanceof BadResponseException) {
            $statusCode = $previous->getResponse()->getStatusCode();
            $statusName = 'Unknown Status';
            $statuses = HttpStatusRepository::getInstance();

            /* Do not create exception if code is ignored */
            if($this->isCodeIgnored($statusCode)) {
                return null;
            }

            /* Get more information about the status code if possible */
            if($statuses->codeExists($statusCode)) {
                $status = $statuses->getByCode($statusCode);
                $statusName = $status->getName();
            }

            $msg = "Server responded with code $statusCode ($statusName)";
            return new HttpRequestServerException($msg, $statusCode, $previous);
        }

        return null;
    }

    public function ignoreErrorCodes(array $codes)
    {
        foreach($codes as $code) {
            $this->ignoreErrorCode($code);
        }
    }

    public function ignoreErrorCode(int $code, bool $ignore = true)
    {
        $this->ignoredErrorCodes[$code] = $ignore;
    }

    public function isCodeIgnored(int $code) : bool
    {
        return in_array($code, array_keys($this->ignoredErrorCodes));
    }
}
