<?php

namespace Adldap\Events;

interface DispatcherInterface
{
    /**
     * Register an event listener with the dispatcher.
     *
     * @param array|string $events
     * @param mixed $listener
     *
     * @return void
     */
    public function listen(array|string $events, mixed $listener): void;

    /**
     * Determine if a given event has listeners.
     *
     * @param string $eventName
     *
     * @return bool
     */
    public function hasListeners(string $eventName): bool;

    /**
     * Fire an event until the first non-null response is returned.
     *
     * @param object|string $event
     * @param mixed $payload
     *
     * @return array|string|null
     */
    public function until(object|string $event, mixed $payload = []): array|string|null;

    /**
     * Fire an event and call the listeners.
     *
     * @param object|string $event
     * @param mixed $payload
     * @param bool $halt
     *
     * @return array|null
     */
    public function fire(object|string $event, mixed $payload = [], bool $halt = false): ?array;

    /**
     * Fire an event and call the listeners.
     *
     * @param object|string $event
     * @param mixed $payload
     * @param bool $halt
     *
     * @return array|string|null
     */
    public function dispatch(object|string $event, mixed $payload = [], bool $halt = false): array|string|null;

    /**
     * Get all of the listeners for a given event name.
     *
     * @param string $eventName
     *
     * @return array
     */
    public function getListeners(string $eventName): array;

    /**
     * Remove a set of listeners from the dispatcher.
     *
     * @param string $event
     *
     * @return void
     */
    public function forget(string $event): void;
}
