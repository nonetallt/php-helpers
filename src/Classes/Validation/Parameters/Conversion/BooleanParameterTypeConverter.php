<?php

namespace Nonetallt\Helpers\Validation\Parameters\Conversion;

class BooleanParameterTypeConverter extends ParameterTypeConverter
{
    protected function shouldConvert($value) : bool
    {
        return ! is_bool($value);
    }

    public function convert($value) : ParameterTypeConversionResult
    {
        $result = new ParameterTypeConversionResult();

        if($value === 'true' || $value === 1 || $value === '1') $result->setValue(true);
        elseif($value === 'false' || $value === 0 || $value === '0') $result->setValue(false);
        else $this->fail($value);

        return $result;
    }
}
