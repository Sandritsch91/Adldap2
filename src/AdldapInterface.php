<?php

namespace Adldap;

use Adldap\Configuration\DomainConfiguration;
use Adldap\Connections\ConnectionInterface;
use Adldap\Connections\ProviderInterface;

interface AdldapInterface
{
    /**
     * Add a provider by the specified name.
     *
     * @param DomainConfiguration|ProviderInterface|array $config
     * @param string $name
     * @param ConnectionInterface|null $connection
     *
     * @return $this
     * @throws \InvalidArgumentException When an invalid type is given as the configuration argument.
     */
    public function addProvider(
        DomainConfiguration|ProviderInterface|array $config,
        string $name,
        ?ConnectionInterface $connection = null
    ): AdldapInterface;

    /**
     * Returns all of the connection providers.
     *
     * @return array
     */
    public function getProviders(): array;

    /**
     * Retrieves a Provider using its specified name.
     *
     * @param string $name
     *
     * @return ProviderInterface
     * @throws AdldapException When the specified provider does not exist.
     *
     */
    public function getProvider(string $name): ProviderInterface;

    /**
     * Sets the default provider.
     *
     * @param string $name
     * @return void
     * @throws AdldapException When the specified provider does not exist.
     */
    public function setDefaultProvider(string $name): void;

    /**
     * Retrieves the first default provider.
     *
     * @return ProviderInterface
     * @throws AdldapException When no default provider exists.
     *
     */
    public function getDefaultProvider(): ProviderInterface;

    /**
     * Removes a provider by the specified name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function removeProvider(string $name): static;

    /**
     * Connects to the specified provider.
     *
     * If no username and password is given, then the providers
     * configured admin credentials are used.
     *
     * @param string|null $name
     * @param string|null $username
     * @param string|null $password
     *
     * @return ProviderInterface
     */
    public function connect(
        ?string $name = null,
        ?string $username = null,
        ?string $password = null
    ): ProviderInterface;

    /**
     * Call methods upon the default provider dynamically.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters);
}
