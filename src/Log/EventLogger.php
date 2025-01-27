<?php

namespace Adldap\Log;

use Adldap\Auth\Events\Event as AuthEvent;
use Adldap\Auth\Events\Failed;
use Adldap\Models\Events\Event as ModelEvent;
use Adldap\Query\Events\QueryExecuted as QueryEvent;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class EventLogger
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Logs the given event.
     *
     * @param mixed $event
     * @throws \ReflectionException
     */
    public function log(mixed $event): void
    {
        if ($event instanceof AuthEvent) {
            $this->auth($event);
        } elseif ($event instanceof ModelEvent) {
            $this->model($event);
        } elseif ($event instanceof QueryEvent) {
            $this->query($event);
        }
    }

    /**
     * Logs an authentication event.
     *
     * @param AuthEvent $event
     *
     * @return void
     * @throws \ReflectionException
     */
    public function auth(AuthEvent $event): void
    {
        if (isset($this->logger)) {
            $connection = $event->getConnection();

            $message = "LDAP ({$connection->getHost()})"
                . " - Connection: {$connection->getName()}"
                . " - Operation: {$this->getOperationName($event)}"
                . " - Username: {$event->getUsername()}";

            $result = null;
            $type = 'info';

            if (is_a($event, Failed::class)) {
                $type = 'warning';
                $result = " - Reason: {$connection->getLastError()}";
            }

            $this->logger->$type($message . $result);
        }
    }

    /**
     * Logs a model event.
     *
     * @param ModelEvent $event
     *
     * @return void
     * @throws \ReflectionException
     */
    public function model(ModelEvent $event): void
    {
        if (isset($this->logger)) {
            $model = $event->getModel();

            $on = get_class($model);

            $connection = $model->getQuery()->getConnection();

            $message = "LDAP ({$connection->getHost()})"
                . " - Connection: {$connection->getName()}"
                . " - Operation: {$this->getOperationName($event)}"
                . " - On: $on"
                . " - Distinguished Name: {$model->getDn()}";

            $this->logger->info($message);
        }
    }

    /**
     * Logs a query event.
     *
     * @param QueryEvent $event
     *
     * @return void
     * @throws \ReflectionException
     */
    public function query(QueryEvent $event): void
    {
        if (isset($this->logger)) {
            $query = $event->getQuery();

            $connection = $query->getConnection();

            $selected = implode(',', $query->getSelects());

            $message = "LDAP ({$connection->getHost()})"
                . " - Connection: {$connection->getName()}"
                . " - Operation: {$this->getOperationName($event)}"
                . " - Base DN: {$query->getDn()}"
                . " - Filter: {$query->getUnescapedQuery()}"
                . " - Selected: ($selected)"
                . " - Time Elapsed: {$event->getTime()}";

            $this->logger->info($message);
        }
    }

    /**
     * Returns the operational name of the given event.
     *
     * @param mixed $event
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getOperationName(mixed $event): string
    {
        return (new ReflectionClass($event))->getShortName();
    }
}
