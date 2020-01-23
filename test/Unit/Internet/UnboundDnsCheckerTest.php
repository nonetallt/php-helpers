<?php

namespace Test\Unit\Internet;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Dns\UnboundDnsChecker;

class UnboundDnsCheckerTest extends TestCase
{
    private $dns;

    public function setUp() : void
    {
        parent::setUp();
        $this->dns = new UnboundDnsChecker();
    }

    /**
     * @group remote
     */
    public function testRecordExistsReturnsFalseWhenQueryingUsingNonExistentDomain()
    {
        $this->assertFalse($this->dns->recordExists('aaa'));
    }

    /**
     * @group remote
     */
    public function testRecordExistsReturnsTrueWhenRecordExists()
    {
        $this->assertTrue($this->dns->recordExists('google.com'));
    }
}
