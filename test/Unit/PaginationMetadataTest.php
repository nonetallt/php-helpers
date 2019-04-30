<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Pagination\PaginationMetadata;

class PaginationMetadataTest extends TestCase
{
    public function testTotalEntriesCannotBeLessThan0()
    {
        $this->expectExceptionMessage('Total entries must be at least 0');
        $pagination = new PaginationMetadata(-1, 1, 1);
    }

    public function testEntriesPerPageCannotBeLessThan1()
    {
        $this->expectExceptionMessage('Entries per page must be at least 1');
        $pagination = new PaginationMetadata(1, -1, 1);
    }

    public function testCurrentPageCannotBeLessThan1()
    {
        $this->expectExceptionMessage('Current page must be at least 1');
        $pagination = new PaginationMetadata(1, 1, -1);
    }

    public function testGetTotalPagesReturnsCorrectAmount()
    {
        $pagination = new PaginationMetadata(3500, 1000);
        $this->assertEquals(4, $pagination->getTotalPages());
    }

    public function testCountEntriesOnLastPageReturnsCorrectAmount()
    {
        $pagination = new PaginationMetadata(3500, 1000);
        $this->assertEquals(500, $pagination->countEntriesOnLastPage());
    }

    public function testCurrentPageCannotBeGreaterThanTotalAmountOfPages()
    {
        $this->expectExceptionMessage('Current page must exist, given: 5 is greater than total number of pages: 4');
        $pagination = new PaginationMetadata(3500, 1000, 5);
    }

    public function testToArrayWorks()
    {
        $pagination = new PaginationMetadata(3500, 1000, 3);
        $expected = [
            'total_entries'        => 3500,
            'total_pages'          => 4,
            'entries_per_page'     => 1000,
            'current_page'         => 3,
            'entries_on_last_page' => 500
        ];

        $this->assertEquals($expected, $pagination->toArray());
    }
}
