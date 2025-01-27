<?php

namespace Adldap;

use Adldap\Configuration\ConfigurationException;
use Adldap\Configuration\DomainConfiguration;
use Adldap\Connections\ConnectionInterface;
use Adldap\Connections\Ldap;
use Adldap\Connections\Provider;
use Adldap\Connections\ProviderInterface;
use Adldap\Events\DispatchesEvents;
use Adldap\Log\EventLogger;
use Adldap\Log\LogsInformation;
use InvalidArgumentException;

class Adldap implements AdldapInterface
{
    use DispatchesEvents;
    use LogsInformation;

    /**
     * The default provider name.
     *
     * @var string
     */
    protected string $default = 'default';

    /**
     * The connection providers.
     *
     * @var array
     */
    protected array $providers = [];

    /**
     * The events to register listeners for during initialization.
     *
     * @var array
     */
    protected array $listen = [
        'Adldap\Auth\Events\*',
        'Adldap\Query\Events\*',
        'Adldap\Models\Events\*',
    ];

    /**
     * Constructor.
     * @throws AdldapException
     */
    public function __construct(array $providers = [])
    {
        foreach ($providers as $name => $config) {
            $this->addProvider($config, $name);
        }

        if ($default = key($providers)) {
            $this->setDefaultProvider($default);
        }

        $this->initEventLogger();
    }

    /**
     * {@inheritdoc}
     * @throws ConfigurationException
     */
    public function addProvider(
        DomainConfiguration|ProviderInterface|array $config,
        string $name = 'default',
        ?ConnectionInterface $connection = null
    ): AdldapInterface {
        if ($this->isValidConfig($config)) {
            $config = new Provider($config, $connection ?? new Ldap($name));
        }

        if ($config instanceof ProviderInterface) {
            $this->providers[$name] = $config;

            return $this;
        }

        throw new InvalidArgumentException(
            "You must provide a configuration array or an instance of Adldap\Connections\ProviderInterface."
        );
    }

    /**
     * Determines if the given config is valid.
     *
     * @param mixed $config
     *
     * @return bool
     */
    protected function isValidConfig(mixed $config): bool
    {
        return is_array($config) || $config instanceof DomainConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider(string $name): ProviderInterface
    {
        if (array_key_exists($name, $this->providers)) {
            return $this->providers[$name];
        }

        throw new AdldapException("The connection provider '$name' does not exist.");
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultProvider(string $name = 'default'): void
    {
        if ($this->getProvider($name) instanceof ProviderInterface) {
            $this->default = $name;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultProvider(): ProviderInterface
    {
        return $this->getProvider($this->default);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProvider(string $name): static
    {
        unset($this->providers[$name]);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws AdldapException
     */
    public function connect(?string $name = null, ?string $username = null, ?string $password = null): ProviderInterface
    {
        $provider = $name ? $this->getProvider($name) : $this->getDefaultProvider();

        return $provider->connect($username, $password);
    }

    /**
     * {@inheritdoc}
     * @throws AdldapException
     */
    public function __call(string $method, array $parameters)
    {
        $provider = $this->getDefaultProvider();

        if (!$provider->getConnection()->isBound()) {
            $provider->connect();
        }

        return call_user_func_array([$provider, $method], $parameters);
    }

    /**
     * Initializes the event logger.
     *
     * @return void
     */
    public function initEventLogger(): void
    {
        $dispatcher = static::getEventDispatcher();

        $logger = $this->newEventLogger();

        // We will go through each of our event wildcards and register their listener.
        foreach ($this->listen as $event) {
            $dispatcher->listen($event, function ($eventName, $events) use ($logger) {
                foreach ($events as $event) {
                    $logger->log($event);
                }
            });
        }
    }

    /**
     * Returns a new event logger instance.
     *
     * @return EventLogger
     */
    protected function newEventLogger(): EventLogger
    {
        return new EventLogger(static::getLogger());
    }
}
