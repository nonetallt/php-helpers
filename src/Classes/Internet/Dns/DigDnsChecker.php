<?php

namespace Nonetallt\Helpers\Internet\Dns;

use Symfony\Component\Process\Process;
use Nonetallt\Helpers\Internet\Dns\Exceptions\DnsCheckException;

class DigDnsChecker extends DnsChecker
{
    public static function getSupportedRecordTypes()
    {
        return [
            'A',
            'AAAA',
            'CNAME',
            'MX',
            'NS',
            'PTR',
            'SOA',
            'TXT',
        ];
    }

    protected function executeQuery(string $domain, string $type = null)
    {
        if(is_null($type)) $type = 'all';

        $process = new Process(['dig', $domain, $type]);
        $process->run();

        /* executes after the command finishes */
        if (! $process->isSuccessful()) {
            throw new DnsCheckException('dig command failed.');
        }

        return $process->getOutput();
    }
}
