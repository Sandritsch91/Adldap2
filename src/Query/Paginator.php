<?php

namespace Adldap\Query;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class Paginator implements Countable, IteratorAggregate
{
    /**
     * The complete results array.
     *
     * @var array
     */
    protected array $results = [];

    /**
     * The total amount of pages.
     *
     * @var int
     */
    protected int $pages;

    /**
     * The amount of entries per page.
     *
     * @var int
     */
    protected int $perPage;

    /**
     * The current page number.
     *
     * @var int
     */
    protected int $currentPage;

    /**
     * The current entry offset number.
     *
     * @var int
     */
    protected int $currentOffset;

    /**
     * Constructor.
     *
     * @param array $results
     * @param int $perPage
     * @param int $currentPage
     * @param int $pages
     */
    public function __construct(array $results = [], int $perPage = 50, int $currentPage = 0, int $pages = 0)
    {
        $this->setResults($results)
            ->setPerPage($perPage)
            ->setCurrentPage($currentPage)
            ->setPages($pages)
            ->setCurrentOffset(($this->getCurrentPage() * $this->getPerPage()));
    }

    /**
     * Get an iterator for the entries.
     *
     * @return ArrayIterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator(): ArrayIterator
    {
        $entries = array_slice($this->getResults(), $this->getCurrentOffset(), $this->getPerPage(), true);

        return new ArrayIterator($entries);
    }

    /**
     * Returns the complete results array.
     *
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Returns the total amount of pages
     * in a paginated result.
     *
     * @return int
     */
    public function getPages(): int
    {
        return $this->pages;
    }

    /**
     * Returns the total amount of entries
     * allowed per page.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Returns the current page number.
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Returns the current offset number.
     *
     * @return int
     */
    public function getCurrentOffset(): int
    {
        return $this->currentOffset;
    }

    /**
     * Returns the total amount of results.
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count(): int
    {
        return count($this->results);
    }

    /**
     * Sets the results array property.
     *
     * @param array $results
     *
     * @return Paginator
     */
    protected function setResults(array $results): static
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Sets the total number of pages.
     *
     * @param int $pages
     *
     * @return Paginator
     */
    protected function setPages(int $pages = 0): static
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Sets the number of entries per page.
     *
     * @param int $perPage
     *
     * @return Paginator
     */
    protected function setPerPage(int $perPage = 50): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Sets the current page number.
     *
     * @param int $currentPage
     *
     * @return Paginator
     */
    protected function setCurrentPage(int $currentPage = 0): static
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Sets the current offset number.
     *
     * @param int $offset
     *
     * @return Paginator
     */
    protected function setCurrentOffset(int $offset = 0): static
    {
        $this->currentOffset = $offset;

        return $this;
    }
}
