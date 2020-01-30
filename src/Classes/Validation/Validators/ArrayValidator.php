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
    private $properties;

    public function __construct(bool $required = false, string $validate = null, $validateItems = null, array $properties = [])
    {
        $this->setIsRequired($required);
        $this->setProperties($properties);
        $this->setValueValidator($validate);
        $this->setItemValidationRules($validateItems);
    }

    /**
     * Get a prettified version of the validation tree
     *
     * @return string $tree
     *
     */
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
    public function validate($value, string $path = 'schema', bool $strict = false) : ValidationResult
    {
        $exceptions = new ValidationExceptionCollection();

        $result = $this->validateValue($path, $value);
        $valueExceptions = $result->getExceptions();
        $exceptions = $exceptions->merge($valueExceptions);

        if(is_array($value) && $valueExceptions->isEmpty()) {
            $result = $this->validateItems($path, $value, $strict);
            $exceptions = $exceptions->merge($result->getExceptions());

            $result = $this->validateProperties($path, $value, $strict);
            $exceptions = $exceptions->merge($result->getExceptions());
        }

        return new ValidationResult($exceptions);
    }

    private function validateValue(string $path, $value, bool $strict = false) : ValidationResult
    {
        if($this->valueValidator !== null) {
            $exceptions = $this->valueValidator->validate($path, $value, $strict);
            return new ValidationResult($exceptions);
        }

        return new ValidationResult();
    }

    private function validateItems(string $path, array $items, bool $strict = false) : ValidationResult
    {
        $result = new ValidationResult();

        if($this->itemValidator === null) {
            return $result;
        }

        foreach($items as $key => $value) {
            $newPath = $this->createPath($path, $key);
            $newExceptions = $this->itemValidator->validate($value, $newPath, $strict)->getExceptions();
            $result->getExceptions()->pushAll($newExceptions);
        }

        return $result;
    }

    private function validateProperties(string $path, array $items, bool $strict = false) : ValidationResult
    {
        $exceptions = new ValidationExceptionCollection();

        foreach($this->properties as $key => $validator) {
            if(($validator->isRequired() || $strict) && $key !== null && ! isset($items[$key])) {
                $msg = "Value {$this->createPath($path, $key)} is required";
                $exception = new ValidationException($key, null, $msg);
                $exceptions->push($exception);
            }
        }

        foreach($items as $key => $value) {

            if(isset($this->properties[$key])) {
                $newPath = $this->createPath($path, $key);
                $result = $this->properties[$key]->validate($value, $newPath, $strict);
                $exceptions = $exceptions->merge($result->getExceptions());
                continue;
            }

            /* Value does not exist in schema... */
            if($strict) {
                $newPath = $this->createPath($path, $key);
                $msg = "Value $newPath not expected";
                $exception = new ValidationException($newPath, $value, $msg);
                $exceptions->push($exception);
            }
            continue;
        }

        return new ValidationResult($exceptions);
    }

    public function setProperties(array $properties)
    {
        $this->properties = [];

        foreach($properties as $key => $property) {
            $this->properties[$key] = self::fromArray($property); 
        }

        /* Automatically expect that the value should be an array */
        if($this->valueValidator === null) {
            $this->setValueValidator('array');
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
            $this->itemValidator = self::fromArray($rules);
            return;
        }

        if(is_string($rules)) {
            $data = ['validate' => $rules];
            $this->itemValidator = self::fromArray($data);
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

    public function createPath(...$parts) : string
    {
        $path = '';
        $separator = '->';

        foreach($parts as $part) {
            if(! is_string($part) && ! is_numeric($part)) continue;
            if($path !== '') $path .= "$separator$part";
            else $path .= $part;
        }

        return $path;
    }

    /**
     * Get an array representation of the validation logic
     *
     * @param string $path Name of the top level variable used in paths
     * @param string $indexPlaceholder String used to represent unknown iterable array keys
     *
     * @return array $data Array representation of the validation logic
     *
     */
    public function getTree(string $path = 'schema', string $indexPlaceholder = '[ITEM_INDEX]') : array
    {
        $data = [
            'path' => $path,
            'is_required' => $this->isRequired
        ];

        if($this->valueValidator !== null) {
            $data['validate'] = $this->valueValidator->toArray();
        }

        if($this->itemValidator !== null) {
            $newPath = $this->createPath($path, $indexPlaceholder);
            $data['validate_items'] = $this->itemValidator->getTree($newPath);
        }

        foreach($this->properties as $key => $validator) {
            $newPath = $this->createPath($path, $key);
            $data['properties'][] = $validator->getTree($newPath);
        }

        return $data;
    }
}
