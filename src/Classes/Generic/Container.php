<?php

namespace Nonetallt\Helpers\Generic;

use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Validation\Validator;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;

/**
 * Key value pair storage. Perfect for settings.
 */
class Container implements \ArrayAccess
{
    protected $options;
    protected $defaults;
    protected $validator;
    protected $whitelisted;
    protected $propNames;

    public function __construct(array $options = [], array $defaults = [], array $validators = [], array $whitelisted = [])
    {
        $this->propNames = array_keys(get_object_vars($this));
        $this->setWhitelisted($whitelisted);
        $this->setValidationRules($validators);
        $this->setDefaults($defaults);
        $this->setAll($options);

        /* TODO THROW EXCEPTION IF REQUIRED VALUES ARE MISSING */
    }

    public static function prefixOptionName(string $name)
    {
        return "_$name";
    }

    public function isOptionWhitelisted(string $name)
    {
        if(empty($this->whitelisted)) return true;

        foreach($this->whitelisted as $whitelisted) {
            if($name === $whitelisted) return true;
            if(strpos($whitelisted, '%') === false && strpos($whitelisted, '*') === false) continue;

            $pattern = "|^$whitelisted$|";
            $pattern = str_replace('%', '\d+', $pattern);
            $pattern = str_replace('*', '.+', $pattern);

            if(preg_match($pattern, $name) === 1) return true;
        }

        return false;
    }

    /**
     *  Checks if option name is a reserved property name and modifies the name
     *  so both properties can be accessed at the same time.
     */
    public function transformOptionName(string $name)
    {
        /* Not a reserved key, no modification neccesary */
        if(! in_array($name, $this->propNames)) return $name;
        return self::prefixOptionName($name);
    }

    public function validateOptionName(string $name)
    {
        if(! $this->isOptionWhitelisted($name)) {
            $valid = implode(', ', $this->whitelisted);
            $msg = "Invalid option '$name': only whitelisted values are allowed ($valid)";
            throw new \InvalidArgumentException($msg);
        }
    }

    public function validateOptionValue(string $key, $value)
    {
        $rules = $this->validator->getRulesFor($key);

        /* No validator exists for key, skip validation */
        if(empty($rules)) return;

        $errors = [];

        foreach($rules as $rule) {
            $result = $rule->validate($value, $key);
            if($result->passed()) continue;
            $errors[] = $result->getMessage();
        }

        if(! empty($errors)) {
            $err = implode(PHP_EOL, $errors);
            $msg = "Value validation failed with errors:" . PHP_EOL . $err;
            throw new ValidationException($key, $value, $msg);
        }
    }

    /**
     * Set all options, undefined options are set to defaults
     */
    public function setAll(array $options)
    {
        $this->options = [];

        /* Set user supplied values */
        foreach($options as $key => $value) {
            $this->set($key, $value);
        }

        /* Set default values without overriding user supplied values */
        $this->setValuesToDefault(false);
    }

    /**
     * Unsets all keys and applies defaults.
     */
    public function reset()
    {
        $this->options = [];
        $this->setValuesToDefault(false);
    }

    /**
     * Sets keys that have a default value to their defaults.
     *
     * @param bool $override Set true to override user input values with
     * defaults. Set false to preserve values given by user.
     */
    public function setValuesToDefault(bool $override)
    {
        /* Set default values */
        foreach($this->defaults as $optionName => $default) {
            /* Do not override values supplied by user */
            if($this->has($optionName)) continue;
            $this->set($optionName, $default);
        }    
    }

    public function isDefaultValue(string $name, $value) : bool
    {
        /* Default value does not exist for this key */
        if(! isset($this->defaults[$name])) return false;

        /* Find the deafult value */
        $default = $this->defaults[$name];

        /* Check if default value is same as given value */
        return $default === $value;
    }

    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    public function setValidationRules(array $validators)
    {
        $rules = TypedArray::create('string', $validators);
        $this->validator = new Validator($rules);
    }

    public function setWhitelisted(array $whitelisted)
    {
        $this->whitelisted = TypedArray::create('string', $whitelisted);
    }

    public function toArray() : array
    {
        $array = [];
        /* Translate modified keys back to their original values */
        foreach($this->options as $key => $value) {
            if(starts_with($key, '_')) {
                $preModificationKey = substr($key, 1);
                if(in_array($preModificationKey, $this->propNames)) $key = $preModificationKey;
            }
            $array[$key] = $value;
        }
        return $array;
    }

    public function has(string $name)
    {
        return isset($this->options[$name]);
    }

    public function get(string $name)
    {
        return $this->$name;
    }

    public function set(string $name, $value)
    {
        $this->validateOptionName($name);
        $this->validateOptionValue($name, $value);
        $name = $this->transformOptionName($name);
        $this->options[$name] = $value;
    }

    public function __get(string $name)
    {
        /* Check if variable name is a reserved key */
        if(in_array($name, $this->propNames)) {
            /* Check if variable exists in option container with transformed name */
            $value = $this->options[self::prefixOptionName($name)] ?? null;
            return $value;
        }

        $value = $this->options[$name] ?? null;
        return $value;
    }

    public function __set(string $name, $value)
    {
        $this->set($name, $value);
    }

    // ArrayAccess methods
    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) {
            $this->options[] = $value;
        } else {
            $this->options[$offset] = $value;
        }
    }

    public function offsetExists($offset) 
    {
        return isset($this->options[$offset]);
    }

    public function offsetUnset($offset) 
    {
        unset($this->options[$offset]);
    }

    public function offsetGet($offset) 
    {
        return isset($this->options[$offset]) ? $this->options[$offset] : null;
    }
}
