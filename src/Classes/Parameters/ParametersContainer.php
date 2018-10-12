<?php

namespace Nonetallt\Helpers\Parameters;

class ParametersContainer
{
    private $data;
    private $placeholderFormat;
    private $placeholderValues;
    private $accessor;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->placeholderFormat = new PlaceholderFormat('{{$}}');
        $this->accessor = new RecursiveAccessor('->');
    }

    public function __get($key)
    {
        if(! isset($this->data[$key])) {
            throw new \Exception("Undefined parameter '$key'");
        }
        return $this->replacePlaceholderValue($key);
    }

    private function replacePlaceholderValue(string $key)
    {
        $originalValue = $this->data[$key];
        $placeholderValues = [];
        $replacedValue = $originalValue;

        /* Don't try finding placeholders for non-string values */
        if(! is_string($originalValue)) return $originalValue;

        /* Find and replace placeholders with their respective values */
        foreach($this->placeholdersFor($key) as $placeholder) {
            $placeholderKey = $this->placeholderFormat->getPlaceholderFor($placeholder);
            $value = $this->getNestedValue($placeholder, $this->placeholderValues);
            $placeholderValues[$placeholderKey] = $value;
        }

        foreach($placeholderValues as $key => $value) {
            $replacedValue = str_replace($key, $value, $replacedValue);
        }

        return $replacedValue;
    }

    public function placeholdersFor(string $key)
    {
        $value = $this->data[$key];

        /* Non-strings don't have placeholders */
        if(! is_string($value)) return []; 

        $placeholders = [];

        $startFormat = $this->placeholderFormat->getStart();
        $endFormat = $this->placeholderFormat->getEnd();

        $startLen = strlen($startFormat);
        $endLen = strlen($endFormat);

        $start = strpos($value, $startFormat);
        $end = strpos($value, $endFormat);
        
        while($start !== false && $end !== false) {
            
            if($end < $start) break;

            $placeholderLength = $end - $start;
            $placeholder = str_splice($value, $start, $placeholderLength + $endLen);

            /* Remove surrounding */ 
            $placeholder = substr($placeholder, $startLen, strlen($placeholder) - $startLen - $endLen);
            $placeholders[] = $placeholder;

            $start = strpos($value, $startFormat);
            $end = strpos($value, $endFormat);
        }

        return $placeholders;
    }

    public function getNestedValue(string $path, $values)
    {
        if(! is_array($values)) return $values;
        if($path === '') throw new \InvalidArgumentException("Path cannot be an empty string");

        $pathParts = explode($this->accessor->getFormat(), $path);

        /* Remove first part from path */
        $current = array_splice($pathParts, 0, 1)[0];

        /* Check for non-existent path (undefined index) */
        if(! isset($values[$current])) throw new \InvalidArgumentException("Path '$current' is not set");

        $value = $values[$current];

        return $this->getNestedValue(implode($this->accessor->getFormat(), $pathParts), $value);
    }

    public function getPlaceholderValues()
    {
        return $this->placeholderValues;
    }

    public function getPlaceholders()
    {
        $placeholders = [];
        $format = $this->placeholderFormat;

        foreach($this->data as $key => $value) {
            $placeholder = $format->getPlaceholdersInString($value, true);
            $placeholders = array_merge($placeholders, $placeholder);
        }

        return $placeholders;
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

    public function toArray()
    {
        $data = [];
        foreach($this->data as $key => $value) {
            $data[$key] = $this->$key;
        }
        return $data;
    }
}
