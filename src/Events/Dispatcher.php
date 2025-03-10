<?php

namespace Adldap\Events;

use Illuminate\Support\Arr;

/**
 * Class Dispatcher.
 *
 * Handles event listening and dispatching.
 *
 * This code was taken out of the Laravel Framework core
 * with broadcasting and queuing omitted to remove
 * an extra dependency that would be required.
 *
 * @author Taylor Otwell
 *
 * @see https://github.com/laravel/framework
 */
class Dispatcher implements DispatcherInterface
{
    /**
     * The registered event listeners.
     *
     * @var array
     */
    protected array $listeners = [];

    /**
     * The wildcard listeners.
     *
     * @var array
     */
    protected array $wildcards = [];

    /**
     * The cached wildcard listeners.
     *
     * @var array
     */
    protected array $wildcardsCache = [];

    /**
     * {@inheritdoc}
     */
    public function listen(array|string $events, mixed $listener): void
    {
        foreach ((array)$events as $event) {
            if (str_contains($event, '*')) {
                $this->setupWildcardListen($event, $listener);
            } else {
                $this->listeners[$event][] = $this->makeListener($listener);
            }
        }
    }

    /**
     * Setup a wildcard listener callback.
     *
     * @param string $event
     * @param mixed $listener
     *
     * @return void
     */
    protected function setupWildcardListen(string $event, mixed $listener): void
    {
        $this->wildcards[$event][] = $this->makeListener($listener, true);

        $this->wildcardsCache = [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners(string $eventName): bool
    {
        return isset($this->listeners[$eventName]) || isset($this->wildcards[$eventName]);
    }

    /**
     * {@inheritdoc}
     */
    public function until(object|string $event, mixed $payload = []): array|string|null
    {
        return $this->dispatch($event, $payload, true);
    }

    /**
     * {@inheritdoc}
     */
    public function fire(object|string $event, mixed $payload = [], bool $halt = false): ?array
    {
        return $this->dispatch($event, $payload, $halt);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(object|string $event, mixed $payload = [], bool $halt = false): array|string|null
    {
        // When the given "event" is actually an object we will assume it is an event
        // object and use the class as the event name and this event itself as the
        // payload to the handler, which makes object based events quite simple.
        [$event, $payload] = $this->parseEventAndPayload(
            $event,
            $payload
        );

        $responses = [];

        foreach ($this->getListeners($event) as $listener) {
            $response = $listener($event, $payload);

            // If a response is returned from the listener and event halting is enabled
            // we will just return this response, and not call the rest of the event
            // listeners. Otherwise we will add the response on the response list.
            if ($halt && !is_null($response)) {
                return $response;
            }

            // If a boolean false is returned from a listener, we will stop propagating
            // the event to any further listeners down in the chain, else we keep on
            // looping through the listeners and firing every one in our sequence.
            if ($response === false) {
                break;
            }

            $responses[] = $response;
        }

        return $halt ? null : $responses;
    }

    /**
     * Parse the given event and payload and prepare them for dispatching.
     *
     * @param mixed $event
     * @param mixed $payload
     *
     * @return array
     */
    protected function parseEventAndPayload(mixed $event, mixed $payload): array
    {
        if (is_object($event)) {
            [$payload, $event] = [[$event], get_class($event)];
        }

        return [$event, Arr::wrap($payload)];
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners(string $eventName): array
    {
        $listeners = $this->listeners[$eventName] ?? [];

        $listeners = array_merge(
            $listeners,
            $this->wildcardsCache[$eventName] ?? $this->getWildcardListeners($eventName)
        );

        return class_exists($eventName, false)
            ? $this->addInterfaceListeners($eventName, $listeners)
            : $listeners;
    }

    /**
     * Get the wildcard listeners for the event.
     *
     * @param string $eventName
     *
     * @return array
     */
    protected function getWildcardListeners(string $eventName): array
    {
        $wildcards = [];

        foreach ($this->wildcards as $key => $listeners) {
            if ($this->wildcardContainsEvent($key, $eventName)) {
                $wildcards = array_merge($wildcards, $listeners);
            }
        }

        return $this->wildcardsCache[$eventName] = $wildcards;
    }

    /**
     * Determine if the wildcard matches or contains the given event.
     *
     * This function is a direct excerpt from Laravel's Str::is().
     *
     * @param string $wildcard
     * @param string $eventName
     *
     * @return bool
     */
    protected function wildcardContainsEvent(string $wildcard, string $eventName): bool
    {
        $patterns = Arr::wrap($wildcard);

        if (empty($patterns)) {
            return false;
        }

        foreach ($patterns as $pattern) {
            // If the given event is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern == $eventName) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^' . $pattern . '\z#u', $eventName) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add the listeners for the event's interfaces to the given array.
     *
     * @param string $eventName
     * @param array $listeners
     *
     * @return array
     */
    protected function addInterfaceListeners(string $eventName, array $listeners = []): array
    {
        foreach (class_implements($eventName) as $interface) {
            if (isset($this->listeners[$interface])) {
                foreach ($this->listeners[$interface] as $names) {
                    $listeners = array_merge($listeners, (array)$names);
                }
            }
        }

        return $listeners;
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param \Closure|string $listener
     * @param bool $wildcard
     *
     * @return \Closure
     */
    public function makeListener(\Closure|string $listener, bool $wildcard = false): \Closure
    {
        if (is_string($listener)) {
            return $this->createClassListener($listener, $wildcard);
        }

        return function ($event, $payload) use ($listener, $wildcard) {
            if ($wildcard) {
                return $listener($event, $payload);
            }

            return $listener(...array_values($payload));
        };
    }

    /**
     * Create a class based listener.
     *
     * @param string $listener
     * @param bool $wildcard
     *
     * @return \Closure
     */
    protected function createClassListener(string $listener, bool $wildcard = false): \Closure
    {
        return function ($event, $payload) use ($listener, $wildcard) {
            if ($wildcard) {
                return call_user_func($this->parseListenerCallback($listener), $event, $payload);
            }

            return call_user_func_array(
                $this->parseListenerCallback($listener),
                $payload
            );
        };
    }

    /**
     * Parse the class listener into class and method.
     *
     * @param string $listener
     *
     * @return array
     */
    protected function parseListenerCallback(string $listener): array
    {
        return str_contains($listener, '@') ?
            explode('@', $listener, 2) :
            [$listener, 'handle'];
    }

    /**
     * {@inheritdoc}
     */
    public function forget(string $event): void
    {
        if (str_contains($event, '*')) {
            unset($this->wildcards[$event]);
        } else {
            unset($this->listeners[$event]);
        }
    }
}
