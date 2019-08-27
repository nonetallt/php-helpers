<?php

namespace Nonetallt\Helpers\Filesystem\Csv;

use Nonetallt\Helpers\Filesystem\Common\File;

class CsvFile extends File
{
    private $options;

    public function __construct(string $path, array $options = [])
    {
        parent::__construct($path);
        $this->setOptions($options);
    }

    public function getHeaders() : array
    {
        /* If options has no delimiter set, make a guess */
        if(! $this->options->has('delimiter')) {
            $this->options->delimiter = $this->guessDelimiter();
        }

        $line = $this->getLines()->get(0, 1)[0] ?? [];
        return str_getcsv($line, $this->options->delimiter, $this->options->enclosure, $this->options->escape);
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
            $this->options->delimiter = $highestValues[0];
            return $highestValues[0];  
        }

        /* If there is more than 1 value with equal score, resolve conflict */

        /* Use default delimiter if set */
        if($defaultDelimiter !== null) {
            $this->options->delimiter = $defaultDelimiter;
            return $defaultDelimiter;
        }

        /* Otherwise throw exception */
        $msg =" Could not accurately guess delimiter that should be used for file";
        throw new \Exception($msg);
    }

    public function setOptions(array $options)
    {
        $this->options = new Options($options);
    }

    public function getOptions() : Options
    {
        return $this->options;
    }
}
