<?php

namespace Test\Unit\Internet;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Dns\UnboundDnsChecker;

class UnboundDnsCheckerTest extends TestCase
{
    private $dns;

    public function setUp()
    {
        parent::setUp();
        /* $this->dns = new UnboundDnsChecker(); */
    }

    /**
     * @group remote
     */
    public function testRecordExistsReturnsFalseWhenQueryingUsingNonExistentDomain()
    {
        $this->markTestIncomplete('Not implemented because of http client dependency');
        $this->assertFalse($this->dns->recordExists('aaa'));
    }

    /**
     * @group remote
     */
    public function testRecordExistsReturnsTrueWhenRecordExists()
    {
        $this->markTestIncomplete('Not implemented because of http client dependency');
        $this->assertTrue($this->dns->recordExists('google.com'));
    }
}
