<?php

class Paginator
{
    private int $totalItems;
    private int $perPage;
    private int $currentPage;

    // Store pagination values
    public function __construct(int $totalItems, int $perPage, int $currentPage)
    {
        $this->totalItems = max(0, $totalItems);
        $this->perPage = max(1, $perPage);
        $this->currentPage = max(1, $currentPage);
    }

    // Calculate the SQL OFFSET value
    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    // Calculate the total number of pages
    public function totalPages(): int
    {
        return (int) ceil($this->totalItems / $this->perPage);
    }

    // Check if there is a previous page
    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    // Check if there is a next page
    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages();
    }

    // Return the current page number
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    // Return the previous page number
    public function prevPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    // Return the next page number
    public function nextPage(): int
    {
        return min($this->totalPages(), $this->currentPage + 1);
    }
}