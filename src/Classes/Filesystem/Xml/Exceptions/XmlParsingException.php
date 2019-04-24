<?php

namespace Nonetallt\Helpers\Filesystem\Xml\Exceptions;

use Nonetallt\Helpers\Generic\Exceptions\ParsingException;

class XmlParsingException extends ParsingException
{
    public function __construct(string $message = '', int $code = 0, \Exception $previous = null)
    {
        if($message === '') {
            $error = libxml_get_last_error();
            $message = "Could not parse xml: $error->message";
            libxml_clear_errors();
        }

        parent::__construct($message, $code, $previous);
    }
}
