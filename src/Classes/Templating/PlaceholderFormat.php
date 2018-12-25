<?php

namespace Nonetallt\Helpers\Templating;

class PlaceholderFormat
{
    private $start;
    private $end;

    public function __construct(string $format)
    {
        self::validate($format);
        $parts = explode('$', $format, 2);
        $this->start = $parts[0];
        $this->end = $parts[1];
    }

    public static function validate(string $format)
    {
        if(substr_count($format, '$') !== 1) {
            $msg = "Placeholder format '$format' must contain exactly 1 instance of the '$' character";
            throw new \InvalidArgumentException($msg);
        }
    }

    public function __toString()
    {
        return $this->getString();
    }

    public function getString()
    {
        return "$this->start$$this->end";
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function getPlaceholderFor(string $key)
    {
        $placeholder = str_replace('$', $key, $this->getString());
        return $placeholder;
    }

    public function trimPlaceholderString(string $subject)
    {
        str_splice($subject, 0, strlen($this->getStart()));
        str_splice($subject, strlen($subject) - strlen($this->getStart()));
        return $subject;
    }

    public function getPlaceholdersInString(string $str, bool $trimPlaceholders = false)
    {
        $placeholders = [];
        $start = strpos($str, $this->start);
        $end = strpos($str, $this->end) + strlen($this->end) - $start;

        while($start !== false && $end !== false) {

            $placeholder = str_splice($str, $start, $end);
            if($trimPlaceholders) $placeholder = $this->trimPlaceholderString($placeholder);
            
            $placeholders[] = $placeholder;

            /* Calculate next placeholder */
            $start = strpos($str, $this->start);
            $end = strpos($str, $this->end) + strlen($this->end) - $start;
        }

        return $placeholders;
    }
}
