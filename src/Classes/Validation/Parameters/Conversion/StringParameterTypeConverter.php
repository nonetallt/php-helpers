<?php

namespace Nonetallt\Helpers\Validation\Parameters\Conversion;

class StringParameterTypeConverter extends ParameterTypeConverter
{
    protected function shouldConvert($value) : bool
    {
        return ! is_string($value);
    }

    public function convert($value) : ParameterTypeConversionResult
    {
        $result = new ParameterTypeConversionResult();
        $type = gettype($value);

        if(is_scalar($value)) $result->setValue((string)$value);
        else $this->fail($value);

        return $result;
    }
}
