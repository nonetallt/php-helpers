<?php

namespace Nonetallt\Helpers\Validation\Validators;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;
use Nonetallt\Helpers\Validation\ValidationRuleFactory;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;
use Nonetallt\Helpers\Validation\Exceptions\ValidationExceptionCollection;
use Nonetallt\Helpers\Validation\Validators\ValueValidator;
use Nonetallt\Helpers\Validation\Results\ValidationResult;
use Nonetallt\Helpers\Generic\MissingValue;
use Nonetallt\Helpers\Validation\ValidationRuleCollection;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleRequired;

class ArrayValidator
{
    use ConstructedFromArray;

    private $valueValidator;
    private $itemValidator;
    private $properties;

    public function __construct(string $validate = null, $validateItems = null, array $properties = [])
    {
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

        $result = $this->validateValue($path, $value, $strict);
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

            if($strict && ! $this->valueValidator->getRules()->hasRule('required'))  {
                $this->valueValidator->prependRules(new ValidationRuleCollection([new ValidationRuleRequired()]));
            }

            return $this->valueValidator->validate($path, $value, $strict);
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

        /* Run all validators */
        foreach($this->properties as $key => $validator) {
            $value = $items[$key] ?? new MissingValue;
            $newPath = $this->createPath($path, $key);
            $result = $validator->validate($value, $newPath, $strict);
            $exceptions->pushAll($result->getExceptions());
            continue;
        }

        if($strict) {
            /* Find all values that do not exist in schema */
            foreach(array_diff_key($items, $this->properties) as $key => $value) {
                $newPath = $this->createPath($path, $key);
                $msg = "Value $newPath not expected";
                $exception = new ValidationException($newPath, $value, $msg);
                $exceptions->push($exception);
            }
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
            'path' => $path
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
