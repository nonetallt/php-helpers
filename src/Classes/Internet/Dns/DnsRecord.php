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

    private $hostname;
    private $type;
    private $value;
    private $ttl;

    public function __construct(string $hostname, string $type, string $value, int $ttl)
    {
        $this->setHostname($hostname);
        $this->setType($type);
        $this->setValue($value);
        $this->setTTL($ttl);
    }

    public function setHostname(string $hostname)
    {
        $this->hostname = $hostname;
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

    public function setTTL(int $ttl)
    {
        if($ttl < 0) {
            $msg = "TTL must be set greater than 0, $ttl given";
            throw new \InvalidArgumentException($msg);
        } 
        $this->ttl = $ttl;
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getTTL()
    {
        return $this->ttl;
    }
}
