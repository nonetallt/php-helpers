<?php

namespace Nonetallt\Helpers\Parameters;

use App\Domain\Parameters\Parameters;

class ParameterFactory
{
    const VALID_TYPES =  [
        'enum',
        'text',
        'array'
    ];

    public function __construct()
    {

    }

    public function parametersFromArray(array $array)
    {
        $params = [];
        foreach($array as $param) {

            if(! is_array($param)) {
                $type = gettype($param);
                $msg = "Cannot create parameter from $type, array should have a nested array with parameter data";
                throw new \InvalidArgumentException($msg);
            }

            $params[] = $this->parameterFromArray($param);
        }
        return new Parameters($params);
    }

    public function parameterFromArray(array $array)
    {
        $this->validateArray($array);

        return $this->create(
            $array['name'],
            $array['type'],
            $array['options'] ?? []
        );
    }

    public function validateArray(array $array)
    {
        $missing = array_keys_missing(Parameters::REQUIRED_KEYS, $array);

        if(! empty($missing)) {
            $missing = implode(', ', $missing);
            $msg = "Missing required array keys to construct parameter: $missing" ;
            throw new \InvalidArgumentException($msg);
        }
    }


    public function create(string $name, string $type, array $options)
    {
        /* Makes sure value is in array (from php-helpers package) */
        in_array_required($type, self::VALID_TYPES);

        $defaults = [];

        if($type === 'enum') $class =  EnumParameter::class;
        if($type === 'text') $class =  TextParameter::class;
        if($type === 'array') $class =  ArrayParameter::class;

        if(is_null($class)) throw new \Exception('Fatal error');


        $defaults = $class::getDefaultOptions();
        $options = new ParameterOptions($options, $defaults);

        return new $class($name, $options);
    }
}
