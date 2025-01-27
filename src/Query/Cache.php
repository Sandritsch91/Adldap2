<?php

namespace Adldap\Query;

use Closure;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class Cache
{
    /**
     * The cache driver.
     *
     * @var CacheInterface
     */
    protected CacheInterface $store;

    /**
     * Constructor.
     *
     * @param CacheInterface $store
     */
    public function __construct(CacheInterface $store)
    {
        $this->store = $store;
    }

    /**
     * Get an item from the cache.
     *
     * @param string $key
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get(string $key): mixed
    {
        return $this->store->get($key);
    }

    /**
     * Store an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param \DateInterval|\DateTimeInterface|int|null $ttl
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function put(string $key, mixed $value, \DateInterval|\DateTimeInterface|int $ttl = null): bool
    {
        return $this->store->set($key, $value, $ttl);
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param string $key
     * @param \DateInterval|\DateTimeInterface|int|null $ttl
     * @param Closure $callback
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function remember(string $key, \DateInterval|\DateTimeInterface|int|null $ttl, Closure $callback): mixed
    {
        $value = $this->get($key);

        if (!is_null($value)) {
            return $value;
        }

        $this->put($key, $value = $callback(), $ttl);

        return $value;
    }

    /**
     * Delete an item from the cache.
     *
     * @param string $key
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function delete(string $key): bool
    {
        return $this->store->delete($key);
    }
}
