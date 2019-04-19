<?php

namespace Nonetallt\Helpers\Filesystem\Exceptions;

class FileNotFoundException extends FilesystemException
{
    public function __construct(string $path, string $message = "File not found", int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $path, $code, $previous);
    }
}
