<?php

namespace Nonetallt\Helpers\Validation\Validators;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;
use Nonetallt\Helpers\Validation\ValidationRuleFactory;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;
use Nonetallt\Helpers\Validation\Exceptions\ValidationExceptionCollection;
use Nonetallt\Helpers\Validation\Validators\ValueValidator;
use Nonetallt\Helpers\Validation\Results\ValidationResult;

class ArrayValidator
{
    use ConstructedFromArray;

    private $isRequired;
    private $valueValidator;
    private $itemValidator;
    private $properyValidators;

    /**
     * Used to debug multilevel recursion exceptions
     */
    private $path;

    public function __construct(bool $required = false, string $validate = null, $validateItems = null, array $properties = [], string $path = '')
    {
        $this->setPath($path);
        $this->setIsRequired($required);
        $this->setProperties($properties);
        $this->setValueValidator($validate);
        $this->setItemValidationRules($validateItems);
    }

    public function __toString() : string
    {
        return json_encode($this->getTree(), JSON_PRETTY_PRINT);
    }

    /**
     * Validate the given value compared to this schema.
     *
     * @param mixed $value Value to validate
     * @param bool $strict Wether validation should match to schema exactly
     *
     * @return Nonetallt\Helpers\Validation\Results\ValidationResult $result
     *
     */
    public function validate($value, bool $strict = false) : ValidationResult
    {
        $exceptions = new ValidationExceptionCollection();

        $result = $this->validateValue($value);
        $valueExceptions = $result->getExceptions();
        $exceptions = $exceptions->merge($valueExceptions);

        if(is_array($value) && $valueExceptions->isEmpty()) {
            $result = $this->validateItems($value, $strict);
            $exceptions = $exceptions->merge($result->getExceptions());

            $result = $this->validateProperties($value, $strict);
            $exceptions = $exceptions->merge($result->getExceptions());
        }

        return new ValidationResult($exceptions);
    }

    public function validateValue($value) : ValidationResult
    {
        if($this->valueValidator !== null) {
            $exceptions = $this->valueValidator->validate($this->getPath(), $value);
            return new ValidationResult($exceptions);
        }

        return new ValidationResult();
    }

    public function validateItems(array $items, bool $strict = false) : ValidationResult
    {
        $result = new ValidationResult();

        if($this->itemValidator === null) {
            return $result;
        }

        $path = $this->itemValidator->getPath();

        foreach($items as $key => $value) {
            $this->itemValidator->setPath($key);
            $newExceptions = $this->itemValidator->validate($value, $strict)->getExceptions();
            $result->getExceptions()->pushAll($newExceptions);
        }

        return $result;
    }

    public function validateProperties(array $items, bool $strict = false) : ValidationResult
    {
        $exceptions = new ValidationExceptionCollection();

        foreach($this->properyValidators as $validator) {
            if(($validator->isRequired() || $strict) && $validator->getPropertyName() !== null && ! isset($items[$validator->getPropertyName()])) {
                $msg = "Value {$validator->getPath()} is required";
                $exception = new ValidationException($validator->getPropertyName(), null, $msg);
                $exceptions->push($exception);
            }
        }

        foreach($items as $key => $value) {

            if(isset($this->properyValidators[$key])) {
                $result = $this->properyValidators[$key]->validate($value);
                $exceptions = $exceptions->merge($result->getExceptions());
                continue;
            }

            /* Value does not exist in schema... */
            if($strict) {
                $path = $this->getPath($key);
                $msg = "Value $path not expected";
                $exception = new ValidationException($path, $value, $msg);
                $exceptions->push($exception);
            }
            continue;
        }

        return new ValidationResult($exceptions);
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function setProperties(array $properties)
    {
        $this->properyValidators = [];

        foreach($properties as $key => $property) {
            $property['path'] = $this->getPath($key);
            $this->properyValidators[$key] = self::fromArray($property); 
        }
    }

    public function setIsRequired(bool $isRequired)
    {
        $this->isRequired = $isRequired;
    }

    public function setValueValidator(?string $rules)
    {
        if($rules === null) return;

        $factory = new ValidationRuleFactory();
        $rules = $factory->makeRulesFromString($rules);
        $this->valueValidator = new ValueValidator($rules);
    }

    public function setItemValidationRules($rules)
    {
        if($rules === null) return;

        /* Automatically expect that the value should be an array */
        if($this->valueValidator === null) {
            $this->setValueValidator('array');
        }

        if(is_array($rules)) {
            $rules['path'] = $this->getPath();
            $this->itemValidator = self::fromArray($rules);
            return;
        }

        if(is_string($rules)) {
            $data = ['validate' => $rules];
            $data['path'] = $this->getPath();
            $this->itemValidator = self::fromArray($data);

            /* $factory = new ValidationRuleFactory(); */
            /* $rules = $factory->makeRulesFromString($rules); */
            /* $this->itemValidator = new ValueValidator($rules); */
            return;
        }

        $msg = "Item validation rules must be either a string or an array";
        throw new \InvalidArgumentException($msg);
    }

    public function getPropertyName() : ?string
    {
        $parts = explode('->', $this->getPath());
        return $parts[count($parts) -1];
    }

    public function isRequired() : bool
    {
        return $this->isRequired;
    }

    public function getPath(string $key = null) : string
    {
        $separator = '->';

        if($this->path === '' || $key === null) {
            $separator = '';
        }

        return "$this->path{$separator}$key";
    }

    public function getTree() : array
    {
        $data = [
            'path' => $this->getPath(),
            'is_required' => $this->isRequired
        ];

        $data['properties'] = array_map(function($validator) {
            return $validator->getTree();
        }, $this->properyValidators);

        if($this->valueValidator !== null) {
            $data['validate'] = $this->valueValidator->toArray();
        }

        if($this->itemValidator !== null) {
            $data['validate_items'] = $this->itemValidator->toArray();
        }

        return $data;
    }
}
