<?php

namespace Nonetallt\Helpers\Validation\Exceptions;

use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Arrays\TypedArray;

class ValidationException extends \Exception
{
    public function __construct($message, int $code = 0, \Exception $previous = null)
    {
        if(is_array($message)) {
            $message = $this->constructMessage($message);
        }

        if(! is_string($message)) {
            $class = self::class;
            $method = '__construct()';
            $given = (new DescribeObject($message))->describeType();
            $msg = "First argument passed to {$class}::{$method} must be either an array or a string, $given given";
            throw new \InvalidArgumentException($msg);
        }

        parent::__construct($message, $code, $previous);
    }

    protected function constructMessage(array $data) : string
    {
        $data = TypedArray::create('array', $data);
        $message = "";

        foreach($data as $key => $messages) {
            $messages = TypedArray::create('string', $messages);

            $paramErrors = array_map(function($message) {
                return "- $message";
            }, $messages);
            $paramErrors = implode(PHP_EOL, $paramErrors);

            $message .= PHP_EOL . "Validation for $key failed:" . PHP_EOL . $paramErrors;
        }

        return $message;
    }
}
