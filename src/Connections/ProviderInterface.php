<?php

namespace Adldap\Connections;

use Adldap\Auth\BindException;
use Adldap\Auth\Guard;
use Adldap\Auth\GuardInterface;
use Adldap\Configuration\ConfigurationException;
use Adldap\Configuration\DomainConfiguration;
use Adldap\Models\Factory;
use Adldap\Schemas\SchemaInterface;

interface ProviderInterface
{
    /**
     * Constructor.
     *
     * @param array|DomainConfiguration $configuration
     * @param ConnectionInterface|null $connection
     */
    public function __construct(DomainConfiguration|array $configuration, ?ConnectionInterface $connection);

    /**
     * Returns the current connection instance.
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface;

    /**
     * Returns the current configuration instance.
     *
     * @return DomainConfiguration
     */
    public function getConfiguration(): DomainConfiguration;

    /**
     * Returns the current Guard instance.
     *
     * @return ?Guard
     */
    public function getGuard(): ?Guard;

    /**
     * Returns a new default Guard instance.
     *
     * @param ConnectionInterface $connection
     * @param DomainConfiguration $configuration
     *
     * @return Guard
     */
    public function getDefaultGuard(
        ConnectionInterface $connection,
        DomainConfiguration $configuration
    ): Guard;

    /**
     * Sets the current connection.
     *
     * @param ConnectionInterface|null $connection
     *
     * @return $this
     */
    public function setConnection(?ConnectionInterface $connection = null): static;

    /**
     * Sets the current configuration.
     *
     * @param array|DomainConfiguration $configuration
     *
     * @throws ConfigurationException
     */
    public function setConfiguration(DomainConfiguration|array $configuration = []);

    /**
     * Sets the current LDAP attribute schema.
     *
     * @param SchemaInterface|null $schema
     *
     * @return $this
     */
    public function setSchema(?SchemaInterface $schema = null): static;

    /**
     * Returns the current LDAP attribute schema.
     *
     * @return SchemaInterface
     */
    public function getSchema(): SchemaInterface;

    /**
     * Sets the current Guard instance.
     *
     * @param GuardInterface $guard
     *
     * @return $this
     */
    public function setGuard(GuardInterface $guard): static;

    /**
     * Returns a new Model factory instance.
     *
     * @return Factory
     */
    public function make(): Factory;

    /**
     * Returns a new Search factory instance.
     *
     * @return \Adldap\Query\Factory
     */
    public function search(): \Adldap\Query\Factory;

    /**
     * Returns a new Auth Guard instance.
     *
     * @return Guard
     */
    public function auth(): Guard;

    /**
     * Connects and Binds to the Domain Controller.
     *
     * If no username or password is specified, then the
     * configured administrator credentials are used.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @return ProviderInterface
     * @throws ConnectionException        If upgrading the connection to TLS fails
     *
     * @throws BindException If binding to the LDAP server fails.
     */
    public function connect(?string $username = null, ?string $password = null): ProviderInterface;
}
