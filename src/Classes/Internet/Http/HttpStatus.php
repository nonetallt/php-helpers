<?php

namespace Nonetallt\Helpers\Internet\Http;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

class HttpStatus
{
    use ConstructedFromArray;

    private $code;
    private $name;
    private $description;
    private $shouldRetry;
    private $standard;

    public function __construct(int $code, string $name, string $description, bool $shouldRetry, ?string $standard = null)
    {
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->shouldRetry = $shouldRetry;
        $this->standard = $standard;
    }

    public static function arrayValidationRules()
    {
        return [ 
            'code'        => 'integer|min:100|max:599',
            'name'        => 'string',
            'description' => 'string',
            'shouldRetry' => 'boolean',
            'standard'    => 'string',
        ];
    }

    public static function arrayToConstructorMapping()
    {
        return [
            'should_retry' => 'shouldRetry'
        ];
    }

    public function __toString() : string
    {
        return "$this->code $this->name";
    }

    public function getCode() : int
    {
        return $this->code;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function shouldRetry() : bool
    {
        return $this->shouldRetry;
    }

    public function getStandard()
    {
        return $this->standard;
    }

    public function toArray()
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'standard' => $this->standard,
        ];
    }
}
