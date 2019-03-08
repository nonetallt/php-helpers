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

    private function findStarts(string $str)
    {
        $starts = [];
        $offset = 0;

        do {
            $start = strpos($str, $this->start, $offset);
            $offset = $start + strlen($this->start);
            if($start !== false) $starts[] = $start;
        } 
        while($start !== false);
        return $starts;
    }

    private function findEnds(string $str)
    {
        $ends = [];
        $offset = 0;

        do {
            $end = strpos($str, $this->end, $offset);
            $offset = $end + strlen($this->end);
            if($end !== false) $ends[] = $end;
        } 
        while($end !== false);
        return $ends;
    }

    /**
     * TODO should be refactored somewhere else
     */
    private function findPairs(array $starts, array $ends, bool $reverseResult = false)
    {
        $pairs = [];

        $starts = array_map(function($item) {
            return ['type' => 'start', 'position' => $item];
        }, $starts);

        $ends = array_map(function($item) {
            return ['type' => 'end', 'position' => $item];
        }, $ends);

        $positions = PositionLine::fromArray(array_merge($starts, $ends));
        $pairs = $positions->getPairs($reverseResult);
        
        return $pairs;
    }

    public function getPlaceholdersInString(string $str, bool $trimPlaceholders = false, bool $reverseResult = false)
    {
        $placeholders = [];
        $starts = $this->findStarts($str);
        $ends = $this->findEnds($str);
        $startCount = count($starts);
        $endCount = count($ends);

        if($startCount !== $endCount) {
            throw new TemplatingException("Syntax error: start count $startCount does not match end count $endCount");
        }

        $pairs = $this->findPairs($starts, $ends, $reverseResult);

        $placeholders = [];
        foreach($pairs as $pair) {
            $placeholder = substr($str, $pair['start'], $pair['end'] - $pair['start'] + strlen($this->end));
            if($trimPlaceholders) $placeholder = $this->trimPlaceholderString($placeholder);
            $placeholders[] = $placeholder;
        }

        return $placeholders;


        $start = strpos($str, $this->start);
        $end = strpos($str, $this->end) + strlen($this->end) - $start;

        while($start !== false && $end !== false) {
            $placeholder = str_splice($str, $start, $end);
            if($trimPlaceholders) $placeholder = $this->trimPlaceholderString($placeholder);
            fwrite(STDERR, print_r($placeholder . PHP_EOL, TRUE));
            
            $placeholders[] = $placeholder;

            /* Calculate next placeholder */
            $start = strpos($str, $this->start);
            $end = strpos($str, $this->end) + strlen($this->end) - $start;
        }

        return $placeholders;
    }
}
