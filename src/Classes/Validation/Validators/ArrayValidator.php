<?php

namespace Nonetallt\Helpers\Validation\Validators;

use Nonetallt\Helpers\Arrays\ArraySchema;
use Nonetallt\Helpers\Validation\ValidationRuleFactory;
use Nonetallt\Helpers\Validation\ValueValidator;
use Nonetallt\Helpers\Validation\Exceptions\ValidationExceptionCollection;
use Nonetallt\Helpers\Validation\Results\ValidationResult;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleArray;

class ArrayValidator
{
    private $itemValidator;
    private $valueValidator;
    private $childValidators;

    /**
     * Used to debug multilevel recursion exceptions
     */
    private $path;

    public function __construct(array $schema, string $path = null)
    {
        $this->setPath($path);

        $valueValidator = $schema['validate'] ?? null;
        $this->setValueValidator($valueValidator);

        $validateItems = $schema['validate_items'] ?? null;
        $this->setItemValidationRules($validateItems);

        $childProperties = $schema['properties'] ?? [];
        foreach($childProperties as $key => $prop) {
            $this->childValidators[$key] = new self($prop, $this->getPath($key));
        }
    }

    public function __toString() : string
    {
        return json_encode($this->getTree(), JSON_PRETTY_PRINT);
    }

    public function setPath(?string $path)
    {
        if($path === 'properties') {
            $path = null;
        }

        $this->path = $path;
    }

    public function setValueValidator(?string $rules)
    {
        if($rules === null) return;

        $factory = new ValidationRuleFactory();
        $rules = $factory->makeRulesFromString($rules);
        $this->valueValidator = new ValueValidator($rules);
    }

    public function setItemValidationRules(?string $rules)
    {
        if($rules === null) return;

        $factory = new ValidationRuleFactory();
        $rules = $factory->makeRulesFromString($rules);
        $this->itemValidator = new ValueValidator($rules);

        /* Automatically expect that the value should be an array */
        if($this->valueValidator === null) {
            $this->setValueValidator('array');
        }
    }

    public function validate($value) : ValidationResult
    {
        $exceptions = new ValidationExceptionCollection();

        $result = $this->validateValue($value);
        $valueExceptions = $result->getExceptions();
        $exceptions = $exceptions->merge($valueExceptions);

        if(is_array($value) && $valueExceptions->isEmpty()) {
            $result = $this->validateItems($value);
            $exceptions = $exceptions->merge($result->getExceptions());

            $result = $this->validateChildren($value);
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

    public function validateItems(array $items) : ValidationResult
    {
        $exceptions = new ValidationExceptionCollection();

        if($this->itemValidator !== null) {
            foreach($items as $key => $value) {
                $newExceptions = $this->itemValidator->validate($this->getPath($key), $value);
                $exceptions = $exceptions->merge($newExceptions);
            }
        }

        return new ValidationResult($exceptions);
    }

    public function validateChildren(array $items) : ValidationResult
    {
        $exceptions = new ValidationExceptionCollection();

        foreach($items as $key => $value) {
            $validator = $this->childValidators[$key] ?? null;
            if($validator === null) continue;

            $result = $validator->validate($value);
            $exceptions = $exceptions->merge($result->getExceptions());
        }

        return new ValidationResult($exceptions);
    }

    public function getPath(string $key = null) : string
    {
        $separator = '->';

        if($this->path === null || $key === null) {
            $separator = '';
        }

        return "$this->path{$separator}$key";
    }

    public function getTree() : array
    {
        $data = [
            'path' => $this->getPath()
        ];

        if($this->childValidators !== null) {
            $data['children'] = array_map(function($validator) {
                return $validator->getTree();
            }, $this->childValidators);
        }

        if($this->valueValidator !== null) {
            $data['validate'] = $this->valueValidator->toArray();
        }

        if($this->itemValidator !== null) {
            $data['validate_items'] = $this->itemValidator->toArray();
        }

        return $data;
    }
}
