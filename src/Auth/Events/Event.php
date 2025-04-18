<?php

namespace Adldap\Auth\Events;

use Adldap\Connections\ConnectionInterface;

abstract class Event
{
    /**
     * The connection that the username and password is being bound on.
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * The username that is being used for binding.
     *
     * @var string
     */
    protected string $username;

    /**
     * The password that is being used for binding.
     *
     * @var string
     */
    protected string $password;

    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param string $username
     * @param string $password
     */
    public function __construct(ConnectionInterface $connection, string $username, string $password)
    {
        $this->connection = $connection;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Returns the events connection.
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * Returns the authentication events username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Returns the authentication events password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
