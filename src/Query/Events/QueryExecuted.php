<?php

namespace Adldap\Query\Events;

use Adldap\Query\Builder;

class QueryExecuted
{
    /**
     * The LDAP filter that was used for the query.
     *
     * @var Builder
     */
    protected Builder $query;

    /**
     * The number of milliseconds it took to execute the query.
     *
     * @var float|null
     */
    protected ?float $time;

    /**
     * Constructor.
     *
     * @param Builder $query
     * @param float|null $time
     */
    public function __construct(Builder $query, ?float $time = null)
    {
        $this->query = $query;
        $this->time = $time;
    }

    /**
     * Returns the LDAP filter that was used for the query.
     *
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Returns the number of milliseconds it took to execute the query.
     *
     * @return float|null
     */
    public function getTime(): ?float
    {
        return $this->time;
    }
}
