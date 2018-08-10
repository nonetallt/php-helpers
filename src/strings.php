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
