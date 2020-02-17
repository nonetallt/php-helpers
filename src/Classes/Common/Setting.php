<?php

namespace Nonetallt\Helpers\Common;

use Nonetallt\Helpers\Validation\Validators\ArrayValidator;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;
use Nonetallt\Helpers\Generic\MissingValue;
use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

/**
 * Other than the setting value, setting is ment to be a read-only class that
 * defines the setting schema and stores the value set by user.
 *
 */
class Setting
{
    use ConstructedFromArray;

    private $name;
    private $value;
    private $default;
    private $validator;

    public function __construct(string $name, ?ArrayValidator $validator = null, $default = MissingValue::class)
    {
        $this->value = new MissingValue();
        $this->setName($name);
        $this->setValidator($validator);
        $this->setDefaultValue($default);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getValue(bool $useDefault = true)
    {
        if($this->hasValue()) {
            if(is_callable($this->value)) return ($this->value)();
            return $this->value;
        }

        if($useDefault && $this->hasDefaultValue()) {
            if(is_callable($this->default)) return ($this->default)();
            return $this->default;
        }

        return new MissingValue;
    }

    public function setValue($value)
    {
        $this->validateValue($value);
        $this->value = $value;
    }

    public function getValidator() : ?ArrayValidator
    {
        return $this->validator;
    }

    public function validateValue($value)
    {
        if($this->validator === null) {
            return;
        }

        $result = $this->validator->validate($value, "of setting $this->name");

        if($result->failed()) {
            $msg = (string)$result->getExceptions();
            throw new ValidationException($this->name, $value, $msg);
        }
    }

    public function hasValue() : bool
    {
        return ! $this->value instanceof MissingValue;
    }

    public function hasDefaultValue() : bool
    {
        return ! $this->default instanceof MissingValue;
    }

    public function hasUsableValue() : bool
    {
        return $this->hasValue() || $this->hasDefaultValue();
    }

    public function getDefaultValue()
    {
        return $this->default;
    }

    protected function setName(string $name)
    {
        $this->name = $name;
    }

    protected function setDefaultValue($default)
    {
        if($default === MissingValue::class) {
            $default = new MissingValue();
        }

        $this->default = $default;
    }

    protected function setValidator(?ArrayValidator $validator)
    {
        $this->validator = $validator;
    }
}
