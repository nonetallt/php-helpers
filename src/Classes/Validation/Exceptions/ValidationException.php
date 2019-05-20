<?php

namespace Nonetallt\Helpers\Validation\Exceptions;

use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Arrays\TypedArray;

class ValidationException extends \Exception
{
    private $name;
    private $value;

    public function __construct(string $name, $value, string $message, int $code = 0, \Exception $previous = null)
    {
        $this->name = $name;
        $this->value = $value;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get name of the validated value
     *
     * @return string $name
     *
     */
    public function getValueName() : string
    {
        return $this->name;
    }

    /**
     * Get the validated value
     *
     * @return mixed $value
     *
     */
    public function getValue()
    {
        return $this->value;
    }
}
