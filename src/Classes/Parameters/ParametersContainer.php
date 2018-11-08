<?php

namespace Nonetallt\Helpers\Parameters;

class ParametersContainer
{
    private $data;
    private $placeholderFormat;
    private $placeholderValues;
    private $accessor;
    private $warnings;
    private $strictValidation;

    public function __construct(array $data, bool $strictValidation = false)
    {
        $this->data = $data;
        $this->placeholderFormat = new PlaceholderFormat('{{$}}');
        $this->accessor = new RecursiveAccessor('->');
        $this->placeholderValues = [];
        $this->warnings = [];
        $this->strictValidation = $strictValidation;
    }

    public function __get($key)
    {
        if(! isset($this->data[$key])) {
            throw new \Exception("Undefined parameter '$key'");
        }
        return $this->replacePlaceholderValue($key);
    }

    private function saveWarning(string $key, \InvalidArgumentException $e)
    {
        $warningsForKey = $this->warnings[$key] ?? [];

        /* Only save unique error messages for each key */
        if(! in_array($e->getMessage(), $warningsForKey)) $this->warnings[$key][] = $e->getMessage();

    }

    private function replacePlaceholderValue(string $valueKey)
    {
        $originalValue = $this->data[$valueKey];
        $placeholderValues = [];
        $replacedValue = $originalValue;

        /* Don't try finding placeholders for non-string values */
        if(! is_string($originalValue)) return $originalValue;

        /* Find and replace placeholders with their respective values */
        foreach($this->placeholdersFor($valueKey) as $placeholder) {
            $placeholderKey = $this->placeholderFormat->getPlaceholderFor($placeholder);
            try {
                $value = $this->accessor->getNestedValue($placeholder, $this->placeholderValues);
                $placeholderValues[$placeholderKey] = $value;
            }
            catch(\InvalidArgumentException $e) {
                if($this->strictValidation) throw $e;
                $this->saveWarning($valueKey, $e);
            }
        }

        foreach($placeholderValues as $key => $value) {
            
            /* If the original value is exactly one placeholder, return the placeholder value */
            if($originalValue === $key) return $value;

            /* Convert object to string if possible */
             if(is_str_convertable($value)) $value = (string)$value;

            if(! is_string($value)) {
                $given = gettype($value);
                $msg = "Cannot replace placeholders for value '$valueKey', value '$originalValue' consist of more than a single placeholder and therefore requires a placeholder that can be converted to a string, $given was given for placeholder $key";
                throw new \Exception($msg);
            } 
            $replacedValue = str_replace($key, $value, $replacedValue);
        }

        return $replacedValue;
    }

    public function placeholdersFor(string $key, bool $trimEnclosure = true)
    {
        $value = $this->data[$key];

        /* Non-strings don't have placeholders */
        if(! is_string($value)) return []; 

        return $this->placeholderFormat->getPlaceholdersInString($value, $trimEnclosure);
    }

    public function getPlaceholderValues()
    {
        return $this->placeholderValues;
    }

    public function getPlaceholders(bool $trimEnclosure = true)
    {
        $placeholders = [];
        $format = $this->placeholderFormat;

        foreach($this->data as $key => $value) {
            if(! is_string($value)) continue;
            $placeholder = $format->getPlaceholdersInString($value, $trimEnclosure);
            $placeholders = array_merge($placeholders, $placeholder);
        }

        return $placeholders;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }

    public function setStrictValidation(bool $strictValidation)
    {
        $this->strictValidation = $strictValidation;
    }

    public function setPlaceholderValues(array $values)
    {
        $this->placeholderValues = $values;
    }

    public function setPlaceholderFormat(string $format)
    {
        $this->placeholderFormat = new PlaceholderFormat($format);
    }

    public function getPlaceholderFormat()
    {
        return $this->placeholderFormat;
    }

    public function getMissingPlaceholderValues()
    {
        $missing = [];

        foreach($this->getPlaceholders() as $placeholder) {
            if($this->accessor->isset($placeholder, $this->placeholderValues)) continue;
            $missing[] = $placeholder;
        }

        return $missing;
    }

    public function toArray()
    {
        $data = [];
        foreach($this->data as $key => $value) {
            $data[$key] = $this->$key;
        }
        return $data;
    }
}
