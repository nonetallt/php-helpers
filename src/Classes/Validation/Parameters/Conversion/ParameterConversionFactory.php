<?php

namespace Nonetallt\Helpers\Validation\Parameters\Conversion;

class ParameterConversionFactory
{
    CONST MAPPING = [
        'boolean' => BooleanParameterTypeConverter::class,
        'integer' => IntegerParameterTypeConverter::class,
        'numeric' => NumericParameterTypeConverter::class,
        'string'  => StringParameterTypeConverter::class
    ];

    public static function convertToType($value, string $type) : ParameterTypeConversionResult
    {
        $result = new ParameterTypeConversionResult($value);
        $converterClass = static::MAPPING[$type] ?? null;

        if($converterClass === null) {
            return $type;
        }

        $converter = new $converterClass();

        if(! $converter->needsConversion($value)) {
            return $result;
        } 

        return $converter->convert($value);
    }
}
