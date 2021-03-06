<?php

namespace Nonetallt\Helpers\Filesystem\Csv;

use Nonetallt\Helpers\Filesystem\Common\File;

class CsvFile extends File
{
    private $settings;

    public function __construct(string $path, array $settings = [])
    {
        parent::__construct($path);
        $this->settings = new CsvSettings();
        $this->settings->setAll($settings);
    }

    /**
     * Get the headers from first non-empty line of the file.
     * If delimiter is not set, attempt to guess it for the headers row.
     *
     * @param bool $renameDuplicates Set to true if you want duplicate headers
     * to be renamed (positional number is appended after header)
     *
     * @param string $renameEmpty Give a name if you want to rename whitespace headers
     * to the given name, it is recommended to use $renameDuplicates with this
     * option
     *
     * @param bool $trimHeaders
     *
     * @return array $headers
     *
     */
    public function getHeaders(bool $renameDuplicates = true, string $renameEmpty = null, bool $trimHeaders = true) : array
    {
        /* If settings has no delimiter set, make a guess */
        if(! $this->settings->getSetting('delimiter')->hasValue()) {
            $this->settings->delimiter = $this->guessDelimiter();
        }

        $line = $this->getLines()->get(0, 1)[0] ?? [];
        $headers = str_getcsv($line, $this->settings->delimiter, $this->settings->enclosure, $this->settings->escape);

        $headers = array_map(function($header) use ($renameEmpty, $trimHeaders) {
            /* Rename empty lines if arg is in use */
            if($renameEmpty !== null && trim($header) === '') $header = $renameEmpty;
            if($trimHeaders) return trim($header);
            return $header;
        }, $headers);

        if($renameDuplicates) {
            $result = [];
            foreach(array_count_values($headers) as $value => $count) {
                if($count === 1) $result[] = $value;
                else {
                    for($n = 0; $n < $count; $n++) {
                        $result[] = $value . ($n + 1);
                    }
                }
            }
            $headers = $result;
        }

        return $headers;
    }
    
    /**
     * Guess the csv delimiter that should be used for this file.
     *
     * @param int $lineCount How many lines should be used for testing from the
     * beginning of the file (empty lines are skipped by default). Higher
     * number increases accuracy at cost of performance.
     *
     * @param string $defaultDelimiter Delimiter to use when no accurate guess
     * can be made. If set to null and there is conflict, an exception will be
     * thrown.
     *
     * @param array $possibleDelimiters Which possible delimiters should be
     * tested for.
     *
     * @return string $delimiter
     *
     */
    public function guessDelimiter(int $lineCount = 1, string $defaultDelimiter = null, array $possibleDelimiters = [',', ';']) : string
    {
        $lines = $this->getLines()->get(0, $lineCount);
        $score = [];

        foreach($lines as $line) {
            foreach($possibleDelimiters as $delimiter) {

                /* Existing score for this delimiter */
                $oldScore = $score[$delimiter] ?? 0;

                /* Score for this line */
                $lineScore = substr_count($line, $delimiter);

                $score[$delimiter] = $oldScore + $lineScore;
            }
        }

        $highestValues = array_keys($score, max($score));

        /* If there is only 1 value, guess should be accurate */
        if(count($highestValues) === 1) {
            $this->settings->delimiter = $highestValues[0];
            return $highestValues[0];  
        }

        /* If there is more than 1 value with equal score, resolve conflict */

        /* Use default delimiter if set */
        if($defaultDelimiter !== null) {
            $this->settings->delimiter = $defaultDelimiter;
            return $defaultDelimiter;
        }

        /* Otherwise throw exception */
        $msg =" Could not accurately guess delimiter that should be used for file";
        throw new \Exception($msg);
    }

    public function getSettings() : CsvSettings
    {
        return $this->settings;
    }
}
