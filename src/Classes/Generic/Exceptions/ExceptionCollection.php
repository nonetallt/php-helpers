<?php

namespace Nonetallt\Helpers\Generic\Exceptions;

use Nonetallt\Helpers\Generic\Collection;

class ExceptionCollection extends Collection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items, \Exception::class);
    }

    public function getMessages() : array
    {
        return $this->map(function($e) {
            return $e->getMessage();
        });
    }

    /**
     * Proxy for hasItemOfClass
     */
    public function hasExceptionOfClass(string $exceptionClass, bool $allowSubclass = true)
    {
        return $this->hasItemOfClass($exceptionClass, $allowSubclass);
    }
}
