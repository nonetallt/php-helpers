<?php

namespace Nonetallt\Helpers\Validation\Parameters\Conversion;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;

class ParameterConversionFactory
{
    use FindsReflectionClasses;

    private $conversionMapping;

    public function __construct()
    {
        $this->conversionMapping = [];
        $refs = $this->findReflectionClasses(__DIR__, __NAMESPACE__, ParameterTypeConverter::class);

        foreach($refs as $ref) {
            $alias = strtolower(str_before($ref->getShortName(), 'ParameterTypeConverter'));
            $this->conversionMapping[$alias] = $ref->name;
        }
    }

    public function convertToType($value, string $type) : ParameterTypeConversionResult
    {
        $result = new ParameterTypeConversionResult($value);

        if(! isset($this->conversionMapping[$type])) {
            return $result;
        }
        $class = $this->conversionMapping[$type];
        $converter = new $class();

        if(! $converter->needsConversion($value)) {
            return $result;
        } 

        return $converter->convert($value);
    }
}
