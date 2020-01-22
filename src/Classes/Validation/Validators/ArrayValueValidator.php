<?php

namespace Nonetallt\Helpers\Validation\Validators;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

class ArrayValueValidator
{
    use ConstructedFromArray;

    private $required;
    private $validators;
    private $rules;
    private $basePath;
    private $pathSeparator;

    public function __construct(string $rules, array $properties = [], array $required = [], string $basePath = null)
    {
        $this->required = $required;
        $this->properties = $properties;
        $this->rules = $rules;
        $this->basePath = $basePath;
        $this->pathSeparator = '->';
    }

    public function validate(array $data) : bool
    {
        /* Check required values */
        /* foreach($this->required as $requiredKey) { */
        /*     if(! isset($data[$requiredKey])) { */
        /*         $msg = "Missing required value at path '{$this->getPath($requiredKey)}'"; */
        /*         throw new \Exception($msg); */
        /*     } */
        /* } */

        $factory = new Validator();
    }

    public function getPath(string $key = null) : string
    {
        $path = $this->basePath ?? '';

        if($path === '') {
            return $key ?? '';
        }

        if($key === null) {
            return $path;
        }

        return "{$path}$this->pathSeparator{$key}";
    }
}
