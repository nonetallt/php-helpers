<?php

namespace Nonetallt\Helpers\Internet\Dns;

class DnsRecord
{
    const TYPES = [
        'A',
        'AAAA',
        'AFSDB',
        'APL',
        'CAA',
        'CDNSKEY',
        'CDS',
        'CERT',
        'CNAME',
        'DHCID',
        'DLV',
        'DNAME',
        'DNSKEY',
        'DS',
        'HIP',
        'IPSECKEY',
        'KEY',
        'KX',
        'LOC',
        'MX',
        'NAPTR',
        'NS',
        'NSEC',
        'NSEC3',
        'NSEC3PARAM',
        'OPENPGPKEY',
        'PTR',
        'RRSIG',
        'RP',
        'SIG',
        'SMIMEA',
        'SOA',
        'SRV',
        'SSHFP',
        'TA',
        'TKEY',
        'TLSA',
        'TSIG',
        'TXT',
        'URI',
    ];

    private $type;
    private $value;

    public function __construct(string $type, string $value)
    {
        $this->setType($type);
        $this->setValue($value);
    }

    public function setType(string $type)
    {
        /* Automatically convert to uc */
        $type = strtoupper($type);

        if(! in_array($type, self::TYPES)) {
            $supported = implode(', ', $recordTypes);
            $msg = "Record type must be one of the supported values: $supported";
            throw new \InvalidArgumentException($msg);
        }

        $this->type = $type;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }
}
