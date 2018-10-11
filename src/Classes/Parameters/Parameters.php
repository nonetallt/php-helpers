<?php

namespace Nonetallt\Helpers\Parameters;

use Nonetallt\Helpers\Arrays\TypedArray;

class Parameters
{
    CONST REQUIRED_KEYS = ['name', 'type'];

    private $parameters;

    public function __construct(array $params)
    {
        $this->parameters = TypedArray::create(Parameter::class, $params);
    }

    public function toArray()
    {
        return $this->parameters;
    }

    public function ofType(string $type)
    {
        $params = [];

        foreach($this->parameters as $parameter) {
            if($type === $parameter->getType()) $params[] = $parameter;
        }

        return $params;
    }

    public function merge(Parameters $parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters->toArray());
    }

    public function validateValues(ParameterValues $values)
    {
        $values = $values->toArray();
        $exceptions = [];
        foreach($this->parameters as $param) {

            try {
                /* Check if this param is found and has correct value */
                required_in_array($param->getName(), array_keys($values));

                $value = $values[$param->getName()];

                if(! $param->validateValue($value)) {
                    $name = $param->getName();
                    $type = gettype($value);
                    $required = $param->getType();

                    $msg = "Value for parameter $name is of invalid type $type, $required required";
                    throw new \InvalidArgumentException($msg);
                }
            }
            catch (\InvalidArgumentException $e) {
                $exceptions[] = $e->getMessage();
            }
        }

        if(! empty($exceptions)) {
            $errors = implode(PHP_EOL, $exceptions);
            $message = "Parameter value validation failed with errors: " . PHP_EOL . $errors;
            throw new \InvalidArgumentException($message);
        }
    }
}
