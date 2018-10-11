<?php

namespace Nonetallt\Helpers\Parameters;

class ParameterOptions
{
    private $data;
    private $defaults;

    public function __construct(array $options, array $defaults)
    {
        $this->data = $options;
        $this->defaults = $defaults;
        $this->setDefaultsForMissingOptions();
    }

    private function setDefaultsForMissingOptions()
    {
        foreach($this->defaults as $key => $value) {
            /* Skip values that have been set */
            if(isset($this->data[$key])) continue;

            $this->data[$key] = $value;
        }
    }

    public function __get($key)
    {
        if(! isset($this->data[$key])) throw new \Exception("Parameter option '$key' is undefined.");
        return $this->data[$key];
    }

    public function toArray()
    {
        return $this->data;
    }
}
