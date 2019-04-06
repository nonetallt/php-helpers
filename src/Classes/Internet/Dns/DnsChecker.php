<?php

namespace Nonetallt\Helpers\Internet\Dns;

abstract class DnsChecker
{
    public function __construct()
    {
    }

    public function recordExists(string $domain, string $type = 'A')
    {
        $recordTypes = $this->getSupportedRecordTypes();

        if(! in_array($type, $recordTypes)) {
            $supported = implode(', ', $recordTypes);
            $msg = "Record type must be one of the values supported by this checker: $supported";
            throw new \InvalidArgumentException($msg);
        }

        $parser = new DigQueryParser($this->executeQuery($domain, $type));
        return $parser->hasAnswer();
    }

    public function recordsExist(array $domains)
    {
        $results = [];

        foreach($domains as $domain => $records) {
            if(! is_array($records)) {
                $msg = "Nested value in 'domains' array must be another array with record types";
                throw new \InvalidArgumentException($msg);
            }

            foreach($records as $record) {
                $results[$domain][$record] = $this->recordExists($domain, $record);
            }
        }

        return $results;
    }

    public function getRecords(string $domain)
    {
        $parser = new DigQueryParser($this->executeQuery($domain));
        return $parser->getAnswer();
    }

    public static abstract function getSupportedRecordTypes();

    protected abstract function executeQuery(string $domain, string $type = null);
}
