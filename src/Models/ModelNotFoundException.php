<?php

namespace Adldap\Models;

use Adldap\AdldapException;

/**
 * Class ModelNotFoundException.
 *
 * Thrown when an LDAP record is not found.
 */
class ModelNotFoundException extends AdldapException
{
    /**
     * The query filter that was used.
     *
     * @var string
     */
    protected string $query;

    /**
     * The base DN of the query that was used.
     *
     * @var string
     */
    protected string $baseDn;

    /**
     * Sets the query that was used.
     *
     * @param string $query
     * @param string $baseDn
     *
     * @return ModelNotFoundException
     */
    public function setQuery(string $query, string $baseDn): static
    {
        $this->query = $query;
        $this->baseDn = $baseDn;

        $this->message = "No LDAP query results for filter: [$query] in: [$baseDn]";

        return $this;
    }
}
