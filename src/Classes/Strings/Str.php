<?php

namespace Nonetallt\Helpers\Strings;

class Str
{
    public static function startsWith(string $subject, ...$starts) : bool
    {
        foreach($starts as $start) {
            if(! is_string($start)) {
                $pos = $index + 1;
                $msg = "Parameter at position $pos must be a string";
                throw new \InvalidArgumentException($msg);
            }

            $subjectStart = substr($subject, 0, strlen($start));
            if($subjectStart === $start) return true;
        }

        return false;
    }

    public static function endsWith(string $subject, ...$ends) : bool
    {
        foreach($ends as $index => $end) {
            if(! is_string($end)) {
                $pos = $index + 1;
                $msg = "Parameter at position $pos must be a string";
                throw new \InvalidArgumentException($msg);
            }

            $subjectEnd = substr($subject, strlen($subject) - strlen($end));
            if($subjectEnd === $end) return true;
        }

        return false;
    }

    public static function removePrefix(string $subject, string $prefix) : string
    {
        if(static::startsWith($subject, $prefix)) {
            $subject = substr($subject, strlen($prefix));
        }

        return $subject;
    }

    public static function removeSuffix(string $subject, string $suffix) : string
    {
        if(ends_with($subject, $suffix)) {
            $len = strlen($subject) - strlen($suffix);
            $subject = substr($subject, 0, $len);
        }

        return $subject;
    }
}
