<?php

namespace SSF\ORM\Cache\Old;

use DateInterval;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use SSF\ORM\Cache\Old\Pool\CacheItem;

class Cache implements CacheInterface
{
    use ValidatesKeys;

    public function __construct(
        private CacheItemPoolInterface $pool
    ){}

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws InvalidCacheKeyException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->pool->getItem($key)->get();
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param DateInterval|int|null $ttl
     * @return bool
     */
    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        return $this->pool->save(new CacheItem($key, $value, $ttl));
    }

    /**
     * @param string $key
     * @return bool
     * @throws InvalidCacheKeyException
     */
    public function delete(string $key): bool
    {
        return $this->pool->deleteItem($key);
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        return $this->pool->clear();
    }

    /**
     * @param iterable $keys
     * @param mixed|null $default
     * @return iterable
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $values = [];
        foreach ($this->pool->getItems((array) $keys) as $item) {
            $values[$item->getKey()] = $item->get() ?? $default;
        }

        return $values;
    }

    /**
     * @param iterable $values
     * @param DateInterval|int|null $ttl
     * @return bool
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->pool->save(new CacheItem($key, $value, $ttl));
        }

        return true;
    }

    /**
     * @param iterable $keys
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteMultiple(iterable $keys): bool
    {
        return $this->pool->deleteItems((array) $keys);
    }

    /**
     * @param string $key
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function has(string $key): bool
    {
        return $this->pool->hasItem($key);
    }
}