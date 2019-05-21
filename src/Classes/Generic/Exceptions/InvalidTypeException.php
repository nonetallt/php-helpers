<?php

namespace Nonetallt\Helpers\Generic\Exceptions;

use Nonetallt\Helpers\Describe\DescribeObject;

class InvalidTypeException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $name, string $expected, $value, int $code = 0, \Exception $previous = null)
    {
        $given = (new DescribeObject($auth))->describeType();
        $msg = "$name must be of the expected type $expected, $given given";
        return new self($msg, $code, $previous);
    }
}
