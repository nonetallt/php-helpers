<?php

namespace Nonetallt\Helpers\Internet\Dns;

use Nonetallt\Helpers\Strings\Str;

class DigQueryParser
{
    private $query;

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    public function hasAnswer()
    {
        $flags = $this->getFlags();
        $answers = $flags['answer'] ?? 0;

        return $answers > 0;
    }

    public function getAnswer()
    {
        $result = [];
        $record = false;

        foreach($this->lines() as $line) {
            if(trim($line) === '') $record = false;
            if(Str::startsWith($line, ';; ANSWER SECTION:')) {
                $record = true;
                continue;
            }
            if($record) $result[] = $line;
        }

        $records = new DnsRecordCollection();

        foreach($result as $line) {
            $records->add($this->parseAnswer($line));
        }

        return $records;
    }
    
    /**
     * Find the line with flags
     */
    public function getFlags()
    {
        foreach($this->lines() as $line) {
            if(! Str::startsWith($line, ';; flags:')) continue;
            return $this->parseFlags($line);
        }

        throw new \Exception("Flags were not found, query:" . str_repeat(PHP_EOL, 2) . $this->query);
    }

    private function lines()
    {
        return explode("\n", $this->query);
    }

    private function parseAnswer(string $answer)
    {
        $parts = explode("\t", $answer);
        $last = count($parts) -1;

        $hostname = $parts[0];

        // Last entry in array is the value of the record
        $value = $parts[$last];

        // Seconds last entry is the type of the record
        $type = $parts[$last -1];

        $ttl = $parts[2];

        return new DnsRecord($hostname, $type, $value, $ttl);
    }

    /**
     * Get an array of the flags from line that contains flags
     */
    private function parseFlags(string $flags)
    {
        /* Get the last part after ; */ 
        $parts = explode(';', $flags);
        $resultsString = trim($parts[count($parts)-1]);
        $keys = [];
        $values = [];

        foreach(explode(' ', $resultsString) as $index => $entry) {
            $entry = str_replace(',', '', $entry);
            $entry = str_replace(':', '', $entry);

            if($index % 2 === 0) $keys[] = strtolower($entry);
            else $values[] = (int)$entry;
        }

        return array_combine($keys, $values);
    }
}
