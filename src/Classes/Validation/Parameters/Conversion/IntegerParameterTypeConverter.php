<?php

namespace Nonetallt\Helpers\Validation\Parameters\Conversion;

class IntegerParameterTypeConverter extends ParameterTypeConverter
{
    protected function shouldConvert($value) : bool
    {
        return ! is_integer($value);
    }

    public function convert($value) : ParameterTypeConversionResult
    {
        $result = new ParameterTypeConversionResult();

        /* Convert numeric strings */
        if(is_numeric($value) && is_string($value)) $result->setValue(intval($value));
        else $this->fail();

        return $result;
    }
}
