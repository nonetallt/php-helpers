<?php

namespace Nonetallt\Helpers\Internet\Dns;

class Domain
{
    private $name;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public static function isValidName(string $name, bool $allowSubdomains = true) : bool
    {
        $dotsInName = substr_count($name, '.');

        if(! $allowSubdomains && $dotsInName !== 1) {
            return false;
        }

        $regex = '|^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$|';
        return preg_match($regex, $name) === 1;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        if(! self::isValidName($name)) {
            throw new \Exception("'$name' does not look like a valid domain name");
        }

        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getTLD() : string
    {
        /* Tld is the part after last dot in name */
        $parts = explode('.', $this->name);
        return $parts[count($parts) -1];
    }

    public function getSLD() : string
    {
        /* Sld is everything exception the last part (tld) */
        $parts = explode('.', $this->name, -1);
        return implode('.', $parts);
    }

    public function isSubdomain()
    {
        return substr_count($this->name, '.') > 1;
    }
}
