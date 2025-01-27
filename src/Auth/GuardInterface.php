<?php

namespace Adldap\Auth;

use Adldap\Configuration\DomainConfiguration;
use Adldap\Connections\ConnectionException;
use Adldap\Connections\ConnectionInterface;

interface GuardInterface
{
    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param DomainConfiguration $configuration
     */
    public function __construct(ConnectionInterface $connection, DomainConfiguration $configuration);

    /**
     * Authenticates a user using the specified credentials.
     *
     * @param string $username The users LDAP username.
     * @param string $password The users LDAP password.
     * @param bool $bindAsUser Whether or not to bind as the user.
     *
     * @return bool
     * @throws UsernameRequiredException When username is empty.
     * @throws PasswordRequiredException When password is empty.
     *
     * @throws BindException             When re-binding to your LDAP server fails.
     */
    public function attempt(string $username, string $password, bool $bindAsUser = false): bool;

    /**
     * Binds to the current connection using the inserted credentials.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     * @throws ConnectionException If upgrading the connection to TLS fails
     *
     * @throws BindException If binding to the LDAP server fails.
     */
    public function bind(string $username = '', string $password = ''): void;

    /**
     * Binds to the current LDAP server using the
     * configuration administrator credentials.
     *
     * @return void
     * @throws BindException When binding as your administrator account fails.
     *
     */
    public function bindAsAdministrator(): void;
}
