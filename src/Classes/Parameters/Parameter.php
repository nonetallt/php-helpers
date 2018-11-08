<?php

namespace Nonetallt\Helpers\Parameters;

abstract class Parameter
{
    protected $name;
    protected $options;

    public function __construct(string $name, ParameterOptions $options)
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ParameterOptions $options
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function nameForHumans()
    {
        $name = $this->getName();
        $name = str_replace('_', ' ', $name);
        $name = ucfirst($name);

        return $name;
    }

    public function getType()
    {
        /* Split by namespace */
        $pieces = explode('\\', get_class($this));
        $class = $pieces[count($pieces) -1];
        $class = lcfirst($class);

        /* Split classname by capital letters */
        $pieces = preg_split('/(?=[A-Z])/', $class);

        return $pieces[0];
    }

    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'type' => $this->getType(),
            'options' => $this->getOptions()->toArray()
        ];
    }

    public abstract function validateValue($value);

    public static abstract function getAvailableOptions();

    public static abstract function getDefaultOptions();
}
