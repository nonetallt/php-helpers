<?php

namespace Nonetallt\Helpers\Validation\Parameters\Conversion;

class ParameterTypeConversionException extends \Exception
{
    public function __construct(ParameterTypeConversionResult $result)
    {
        if(! $result->hasErrors()) {
            $class = self::class;
            $msg = "Can't create $class with empty errors";
            throw new \Exception($msg);
        }

        $message = implode(PHP_EOL, $result->getErrors());
        parent::__construct($message);
    }
}
