<?php

namespace Nonetallt\Helpers\Strings;

class Str
{
    /**
     * Check if string starts with one of the given starts
     *
     */
    public static function startsWith(string $subject, string ...$starts) : bool
    {
        foreach($starts as $start) {
            $subjectStart = substr($subject, 0, strlen($start));
            if($subjectStart === $start) return true;
        }

        return false;
    }

    /**
     * Check if string ends with one of the given endings
     *
     */
    public static function endsWith(string $subject, string ...$ends) : bool
    {
        foreach($ends as $index => $end) {
            $subjectEnd = substr($subject, strlen($subject) - strlen($end));
            if($subjectEnd === $end) return true;
        }

        return false;
    }

    /**
     * Remove prefix from beginning of subject string
     *
     */
    public static function removePrefix(string $subject, string $prefix) : string
    {
        if(static::startsWith($subject, $prefix)) {
            $subject = substr($subject, strlen($prefix));
        }

        return $subject;
    }

    /**
     * Remove a suffix from end of the subject string
     *
     */
    public static function removeSuffix(string $subject, string $suffix) : string
    {
        if(ends_with($subject, $suffix)) {
            $len = strlen($subject) - strlen($suffix);
            $subject = substr($subject, 0, $len);
        }

        return $subject;
    }

    /**
     * Check if string starts with whitespace character
     *
     */
    public static function startsWithWhitespace(string $subject) : bool
    {
        return preg_match('|^\s+|', $subject) === 1;
    }


    /**
     * Explode string with multiple delimiters
     *
     */
    public static function explodeMultiple(string $subject, string ...$delimiters) : array
    {
        /* Get first delimiter */
        $first = $delimiters[0];

        /* Replace all delimiters on the subject with the first one */
        foreach($delimiters as $delimiter) {
            $subject = str_replace($delimiter, $first, $subject);
        }

        return explode($first, $subject);
    }


    /**
     * Remove repeating occurences of character within subject string 
     *
     */
    public static function removeRecurring(string $subject, string $character) : string
    {
        if(strlen($character) !== 1) {
            $message = "Given character must be a string with a length of 1 character. '$character' given.";
            throw new \InvalidArgumentException($message) ;
        } 

        $indexesToRemove = [];
        $lastChar = '';

        for($n = 0; $n < strlen($subject); $n++) {
            $currentChar = substr($subject, $n, 1);
            if($currentChar === $lastChar && $currentChar === $character) $indexesToRemove[] = $n -1;
            $lastChar = $currentChar;
        }

        foreach($indexesToRemove as $index => $pos) {
            /* Index equals the number of indexes removed, 
                adjust position by the amount of characters that were removed 
             */
            $subject = substr_replace($subject, '', $pos - $index, 1);
        }

        return $subject;
    }


    /**
     * Extract a substring from the subject string
     *
     */
    public static function splice(string &$subject, int $start, int $length = null) : string
    {
        if(is_null($length)) $length = strlen($subject);

        $result = substr($subject, $start, $length);
        $subject =  substr($subject, 0, $start) . substr($subject, $start + $length);

        return $result;
    }

    /**
     * Get the part of string after first occurence of another string
     *
     */
    public static function after(string $subject, string $after) : string
    {
        $pos = strpos($subject, $after);

        /* Return subject string if after is not found */
        if($pos === false) return $subject;

        return substr($subject, $pos + strlen($after));
    }


    /**
     * Get the part of string before first occurence of another string
     *
     */
    public static function before(string $subject, string $before) : string
    {
        $pos = strpos($subject, $before);

        /* Return subject string if after is not found */
        if($pos === false) return $subject;

        return substr($subject, 0, $pos);
    }

    /**
     * Check if subject string contains another string
     *
     */
    public static function contains(string $subject, string $another) : bool
    {
        return strpos($subject, $another) !== false;
    }
}
