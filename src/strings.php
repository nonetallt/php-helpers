<?php

if(! function_exists('starts_with')) {
    function starts_with(string $subject, string $start) 
    {
        $subjectStart = substr($subject, 0, strlen($start));
        return $subjectStart === $start;
    }
}

if(! function_exists('ends_with')) {
    function ends_with(string $subject, string $end) 
    {
        $subjectEnd = substr($subject, strlen($subject) - strlen($end));
        return $subjectEnd === $end;
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
