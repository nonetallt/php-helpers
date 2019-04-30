<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Dns\Domain;

class DomainTest extends TestCase
{
    public function testIsValidNameReturnsTrueWhenNameIsValidDomainName()
    {
        $this->assertTrue(Domain::isValidName('foo.com'));
    }

    public function testIsValidNameReturnsFalseWhenNameIsInvalidDomainName()
    {
        $this->assertFalse(Domain::isValidName('foobar'));
    }

    public function testIsValidNameAcceptsSubdomainsByDefault()
    {
        $this->assertTrue(Domain::isValidName('foo.bar.com'));
    }

    public function testIsValidNameReturnsTrueForSubdomainWhenSubdomainArgumentIsTrue()
    {
        $this->assertTrue(Domain::isValidName('foo.bar.com', true));
    }

    public function testIsValidNameReturnsFalseWhenNameIsSubdomainWhenSubdomainArgumentIsFalse()
    {
        $this->assertFalse(Domain::isValidName('foo.bar.com', false));
    }

    public function testIsSudbdomainReturnsFalseForRegularDomains()
    {
        $domain = new Domain('foo.bar');
        $this->assertFalse($domain->isSubdomain());
    }

    public function testIsSudbdomainReturnsTrueForSubdomains()
    {
        $domain = new Domain('foo.bar.baz');
        $this->assertTrue($domain->isSubdomain());
    }

    public function testGetTldReturnsCorrectStringForRegularDomains()
    {
        $domain = new Domain('foo.bar');
        $this->assertEquals('bar', $domain->getTLD());
    }

    public function testGetTldReturnsCorrectStringForSubdomains()
    {
        $domain = new Domain('foo.bar.baz');
        $this->assertEquals('baz', $domain->getTLD());
    }

    public function testGetSldReturnsCorrectStringForRegularDomains()
    {
        $domain = new Domain('foo.bar');
        $this->assertEquals('foo', $domain->getSLD());
    }

    public function testGetSldReturnsCorrectStringForSubdomains()
    {
        $domain = new Domain('foo.bar.baz');
        $this->assertEquals('foo.bar', $domain->getSLD());
    }
}
