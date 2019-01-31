<?php

if(! function_exists('starts_with')) {
    function starts_with(string $subject, ...$starts) 
    {
        foreach($starts as $start) {
            $subjectStart = substr($subject, 0, strlen($start));
            if($subjectStart === $start) return true;
        }
        return false;
    }
}

if(! function_exists('ends_with')) {
    function ends_with(string $subject, ...$ends) 
    {
        foreach($ends as $end) {
            $subjectEnd = substr($subject, strlen($subject) - strlen($end));
            if($subjectEnd === $end) return true;
        }
        return false;
    }
}

if(! function_exists('starts_with_whitespace')) {
    function starts_with_whitespace(string $subject) 
    {
        return preg_match('|^\s+|', $subject) === 1;
    }
}

if(! function_exists('explode_multiple')) {
    function explode_multiple(string $subject, ...$delimiters) 
    {
        /* Get first delimiter */
        $first = $delimiters[0];

        /* Replace all delimiters on the subject with the first one */
        foreach($delimiters as $delimiter) {
            $subject = str_replace($delimiter, $first, $subject);
        }

        return explode($first, $subject);
    }
}

if(! function_exists('str_remove_recurring')) {
    function str_remove_recurring(string $subject, string $character) 
    {
        if(strlen($character) !== 1) {
            $message = "Given character must be a string with a lenght of 1 character. '$character' given.";
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
}

if(! function_exists('str_splice')) {
    function str_splice(string &$subject, int $start, int $length = null)
    {
        if(is_null($length)) $length = strlen($subject);

        $result = substr($subject, $start, $length);
        $subject =  substr($subject, 0, $start) . substr($subject, $start + $length);

        return $result;
    }
}

if(! function_exists('is_str_convertable')) {
    function is_str_convertable($value)
    {
        if(is_null($value)) return true;
        if(is_scalar($value)) return true;
        if(is_object($value) && method_exists($value, '__toString')) return true;

        return false;
    }
}

if(! function_exists('str_after')) {
    function str_after(string $subject, string $after)
    {
        $pos = strpos($subject, $after);

        /* Return subject string if after is not found */
        if($pos === false) return $subject;

        return substr($subject, $pos + strlen($after));
    }
}

if(! function_exists('str_before')) {
    function str_before(string $subject, string $before)
    {
        $pos = strpos($subject, $before);

        /* Return subject string if after is not found */
        if($pos === false) return $subject;

        return substr($subject, 0, $pos);
    }
}
