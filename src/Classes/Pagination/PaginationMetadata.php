<?php

namespace Nonetallt\Helpers\Pagination;

class PaginationMetadata
{
    private $totalEntries;
    private $entriesPerPage;
    private $currentPage;

    public function __construct(int $totalEntries, int $entriesPerPage, int $currentPage = 1)
    {
        $this->setTotalEntries($totalEntries);
        $this->setEntriesPerPage($entriesPerPage);
        $this->setCurrentPage($currentPage);
    }

    public function setTotalEntries(int $totalEntries)
    {
        if($totalEntries < 0) {
            $msg = 'Total entries must be at least 0';
            throw new \InvalidArgumentException($msg);
        }

        $this->totalEntries = $totalEntries;
    }

    public function setEntriesPerPage(int $entriesPerPage)
    {
        if($entriesPerPage < 1) {
            $msg = 'Entries per page must be at least 1';
            throw new \InvalidArgumentException($msg);
        }

        $this->entriesPerPage = $entriesPerPage;
    }

    public function setCurrentPage(int $currentPage)
    {
        if($currentPage < 1) {
            $msg = 'Current page must be at least 1';
            throw new \InvalidArgumentException($msg);
        }

        $total = $this->getTotalPages();
        if($currentPage > $total) {
            $msg = "Current page must exist, given: $currentPage is greater than total number of pages: $total";
            throw new \InvalidArgumentException($msg);
        }

        $this->currentPage = $currentPage;
    }

    public function getNextPages() : array
    {
        $pages = [];
        for($n = $this->currentPage + 1; $n <= $this->getTotalPages(); $n++) {
            $pages[] = $n;
        }
        return $pages;
    }

    public function getPagesLeft() : int
    {
        return $this->getTotalPages() - $this->currentPage;
    }

    public function getTotalEntries() : int
    {
        return $this->totalEntries;
    }

    public function getEntriesPerPage() : int
    {
        return $this->entriesPerPage;
    }

    public function getCurrentPage() : int
    {
        return $this->currentPage;
    }

    public function getTotalPages() : int
    {
        return (int)ceil($this->totalEntries / $this->entriesPerPage);
    }

    public function countEntriesOnLastPage() : int
    {
        return $this->totalEntries % $this->entriesPerPage;
    }

    public function toArray()
    {
        return  [
            'total_entries'        => $this->totalEntries,
            'total_pages'          => $this->getTotalPages(),
            'entries_per_page'     => $this->entriesPerPage,
            'current_page'         => $this->currentPage,
            'entries_on_last_page' => $this->countEntriesOnLastPage()
        ];
    }
}
