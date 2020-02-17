<?php

namespace Nonetallt\Helpers\Generic\Exceptions;

use Nonetallt\Helpers\Generic\Collection;

class ExceptionCollection extends Collection
{
    CONST COLLECTION_TYPE = \Exception::class;
    
    public function getMessages() : array
    {
        return $this->map(function($e) {
            return $e->getMessage();
        });
    }

    public function __toString()
    {
        return implode(PHP_EOL, $this->getMessages());
    }

    /**
     * Proxy for hasItemOfClass
     */
    public function hasExceptionOfClass(string $exceptionClass, bool $allowSubclass = true)
    {
        return $this->hasItemOfClass($exceptionClass, $allowSubclass);
    }

    /**
     * Catch all exceptions of this collections type while inside the callback
     *
     * @param callable $cb
     *
     */
    public function catch(callable $cb)
    {
        try {
            $cb();
        }
        catch(\Exception $e) {
            if(is_a($e, static::COLLECTION_TYPE)) {
                $this->push($e);
            }
            else {
                throw $e;
            }
        }
    }
}
