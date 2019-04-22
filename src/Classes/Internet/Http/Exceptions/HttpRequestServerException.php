<?php

namespace Nonetallt\Helpers\Internet\Http\Exceptions;

/**
 * Caused by the server that the request was sent to. Mainly used for http codes
 * from 4XX and 5XX categories.
 */
class HttpRequestServerException extends HttpRequestException
{
}
