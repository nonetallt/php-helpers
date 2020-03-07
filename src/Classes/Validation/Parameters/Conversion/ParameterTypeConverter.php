<?php

namespace Nonetallt\Helpers\Validation\Parameters\Conversion;

use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Strings\Str;

abstract class ParameterTypeConverter
{
    public function __construct()
    {
    }

    public function needsConversion($value) : bool
    {
        if($value === null) return false;
        return $this->shouldConvert($value);
    }

    protected function fail($value)
    {
        $ref = new \ReflectionClass($this);
        $expected = strtolower(Str::before($ref->getShortName(), 'ParameterTypeConverter'));

        $desc = new DescribeObject($value);
        $given = $desc->describeAsString();
        $msg = "Value '$given' could not be converted to $expected";
        $result = new ParameterTypeConversionResult();
        $result->addError($msg);

        throw new ParameterTypeConversionException($result);
    }

    protected abstract function shouldConvert($value) : bool;

    public abstract function convert($value) : ParameterTypeConversionResult;
}
