<?php

namespace Test\Unit\Internet\Internet\Dns;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Dns\DigDnsChecker;

class DigDnsCheckerTest extends TestCase
{
    private $dns;

    public function setUp() : void
    {
        parent::setUp();
        $this->dns = new DigDnsChecker();
    }

    /**
     * @group dns
     */
    public function testRecordExistsReturnsFalseWhenQueryingUsingNonExistentDomain()
    {
        $this->assertFalse($this->dns->recordExists('aaa'));
    }

    /**
     * @group dns
     */
    public function testRecordExistsReturnsTrueWhenRecordExists()
    {
        $this->assertTrue($this->dns->recordExists('google.com'));
    }

    /**
     * @group dns
     */
    public function testRecordsExistReturnsResultsForEachQueriedEntry()
    {
        $records = [
            'google.com' => ['A'],
            'aaa' => ['A'],
            'yahoo.com' => ['A']
        ];

        $expected = [
            'google.com' => ['A' => true],
            'aaa' => ['A' => false],
            'yahoo.com' => ['A' => true]
        ];

        $this->assertEquals($expected, $this->dns->recordsExist($records));
    }

    /**
     * @group dns
     */
    public function testGetRecordsCanCorrectlySerializeResults()
    {
        $hostname = 'nonetallt.com';

        /* Note, this may change over time */
        $expected = [
            'A' => '198.54.114.169'
        ];

        $this->assertEquals($expected, $this->dns->getRecords($hostname)->toArray());
    }
}
