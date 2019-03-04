<?php

namespace Nonetallt\Helpers\Validation\Parameters\Conversion;

class NumericParameterTypeConverter extends ParameterTypeConverter
{
    protected function shouldConvert($value) : bool
    {
        return ! is_numeric($value) || is_string($value);
    }

    public function convert($value) : ParameterTypeConversionResult
    {
        $result = new ParameterTypeConversionResult();

        /* Convert numeric strings */
        if(is_numeric($value) && is_string($value)) $result->setValue(floatval($value));
        else $this->fail();

        return $result;
    }
}
