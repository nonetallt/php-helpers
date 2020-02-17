<?php

namespace Nonetallt\Helpers\Internet\Http\Exceptions;

use Nonetallt\Helpers\Generic\Exceptions\ExceptionCollection;

class HttpRequestExceptionCollection extends ExceptionCollection
{
    CONST COLLECTION_TYPE = HttpRequestException::class;

    public function hasConnectionErrors()
    {
        return $this->hasItemOfClass(HttpRequestConnectionException::class);
    }

    public function hasClientErrors()
    {
        return $this->hasItemOfClass(HttpRequestClientException::class);
    }

    public function hasServerErrors()
    {

        return $this->hasItemOfClass(HttpRequestServerException::class);
    }

    public function hasResponseErrors()
    {

        return $this->hasItemOfClass(HttpRequestResponseException::class);
    }
}
