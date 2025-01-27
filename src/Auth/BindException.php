<?php

namespace Adldap\Auth;

use Adldap\AdldapException;
use Adldap\Connections\DetailedError;

/**
 * Class BindException.
 *
 * Thrown when binding to an LDAP connection fails.
 */
class BindException extends AdldapException
{
    /**
     * The detailed LDAP error.
     *
     * @var DetailedError
     */
    protected DetailedError $detailedError;

    /**
     * Sets the detailed error.
     *
     * @param DetailedError|null $error
     *
     * @return $this
     */
    public function setDetailedError(?DetailedError $error = null): static
    {
        $this->detailedError = $error;

        return $this;
    }

    /**
     * Returns the detailed error.
     *
     * @return DetailedError|null
     */
    public function getDetailedError(): ?DetailedError
    {
        return $this->detailedError;
    }
}
