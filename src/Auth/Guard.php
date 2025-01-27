<?php

namespace Adldap\Auth;

use Adldap\Auth\Events\Attempting;
use Adldap\Auth\Events\Binding;
use Adldap\Auth\Events\Bound;
use Adldap\Auth\Events\Failed;
use Adldap\Auth\Events\Passed;
use Adldap\Configuration\ConfigurationException;
use Adldap\Configuration\DomainConfiguration;
use Adldap\Connections\ConnectionException;
use Adldap\Connections\ConnectionInterface;
use Adldap\Events\DispatcherInterface;
use Exception;
use Throwable;

/**
 * Class Guard.
 *
 * Binds users to the current connection.
 */
class Guard implements GuardInterface
{
    /**
     * The connection to bind to.
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * The domain configuration to utilize.
     *
     * @var DomainConfiguration
     */
    protected DomainConfiguration $configuration;

    /**
     * The event dispatcher.
     *
     * @var DispatcherInterface
     */
    protected DispatcherInterface $events;

    /**
     * {@inheritdoc}
     */
    public function __construct(ConnectionInterface $connection, DomainConfiguration $configuration)
    {
        $this->connection = $connection;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function attempt(string $username, string $password, bool $bindAsUser = false): bool
    {
        $this->validateCredentials($username, $password);

        $this->fireAttemptingEvent($username, $password);

        try {
            $this->bind(
                $this->applyPrefixAndSuffix($username),
                $password
            );

            $result = true;

            $this->firePassedEvent($username, $password);
        } catch (BindException|ConnectionException|ConfigurationException) {
            // We'll catch the BindException here to allow
            // developers to use a simple if / else
            // using the attempt method.
            $result = false;
        }

        // If we're not allowed to bind as the user,
        // we'll rebind as administrator.
        if ($bindAsUser === false) {
            // We won't catch any BindException here so we can
            // catch rebind failures. However this shouldn't
            // occur if our credentials are correct
            // in the first place.
            try {
                $this->bindAsAdministrator();
            } catch (BindException|ConnectionException|ConfigurationException) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $username = '', string $password = ''): void
    {
        $this->fireBindingEvent($username, $password);

        try {
            if (@$this->connection->bind($username, $password) === true) {
                $this->fireBoundEvent($username, $password);
            } else {
                throw new Exception($this->connection->getLastError(), $this->connection->errNo());
            }
        } catch (Throwable $e) {
            $this->fireFailedEvent($username, $password);

            throw (new BindException($e->getMessage(), $e->getCode(), $e))
                ->setDetailedError($this->connection->getDetailedError());
        }
    }

    /**
     * {@inheritdoc}
     * @throws ConnectionException|ConfigurationException
     */
    public function bindAsAdministrator(): void
    {
        $this->bind(
            $this->configuration->get('username') ?? '',
            $this->configuration->get('password') ?? ''
        );
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return DispatcherInterface
     */
    public function getDispatcher(): DispatcherInterface
    {
        return $this->events;
    }

    /**
     * Sets the event dispatcher instance.
     *
     * @param DispatcherInterface $dispatcher
     *
     * @return void
     */
    public function setDispatcher(DispatcherInterface $dispatcher): void
    {
        $this->events = $dispatcher;
    }

    /**
     * Applies the prefix and suffix to the given username.
     *
     * @param string $username
     *
     * @return string
     * @throws ConfigurationException If account_suffix or account_prefix do not
     *                                                      exist in the providers domain configuration
     *
     */
    protected function applyPrefixAndSuffix(string $username): string
    {
        $prefix = $this->configuration->get('account_prefix');
        $suffix = $this->configuration->get('account_suffix');

        return $prefix . $username . $suffix;
    }

    /**
     * Validates the specified username and password from being empty.
     *
     * @param string $username
     * @param string $password
     *
     * @throws PasswordRequiredException When the given password is empty.
     * @throws UsernameRequiredException When the given username is empty.
     */
    protected function validateCredentials(string $username, string $password): void
    {
        if (empty($username)) {
            // Check for an empty username.
            throw new UsernameRequiredException('A username must be specified.');
        }

        if (empty($password)) {
            // Check for an empty password.
            throw new PasswordRequiredException('A password must be specified.');
        }
    }

    /**
     * Fire the attempting event.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    protected function fireAttemptingEvent(string $username, string $password): void
    {
        if (isset($this->events)) {
            $this->events->fire(new Attempting($this->connection, $username, $password));
        }
    }

    /**
     * Fire the passed event.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    protected function firePassedEvent(string $username, string $password): void
    {
        if (isset($this->events)) {
            $this->events->fire(new Passed($this->connection, $username, $password));
        }
    }

    /**
     * Fire the failed event.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @return void
     */
    protected function fireFailedEvent(?string $username, ?string $password): void
    {
        if (isset($this->events)) {
            $this->events->fire(new Failed($this->connection, $username, $password));
        }
    }

    /**
     * Fire the binding event.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @return void
     */
    protected function fireBindingEvent(?string $username, ?string $password): void
    {
        if (isset($this->events)) {
            $this->events->fire(new Binding($this->connection, $username, $password));
        }
    }

    /**
     * Fire the bound event.
     *
     * @param string $username
     * @param string $password
     *
     * @return void
     */
    protected function fireBoundEvent(string $username, string $password): void
    {
        if (isset($this->events)) {
            $this->events->fire(new Bound($this->connection, $username, $password));
        }
    }
}
