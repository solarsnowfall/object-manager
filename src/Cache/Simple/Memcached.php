<?php

namespace SSF\ORM\Cache\Simple;

use DateInterval;
use SSF\ORM\Cache\Old\TTL;

class Memcached extends AbstractAdapter
{
    public function __construct(
        private \Memcached $memcached
    ){}

    protected function hasKey(string $key): bool
    {
        return false !== $this->memcached->get($key);
    }

    protected function keyExpired(string $key): bool
    {
        return false !== $this->memcached->get($key);
    }

    protected function fetchValue(string $key): mixed
    {
        return $this->memcached->get($key);
    }

    protected function fetchFoundValues(array $keys): array
    {
        return $this->memcached->getMulti($keys);
    }

    protected function storeValue(string $key, mixed $value, DateInterval|int|null $ttl): bool
    {
        return $this->memcached->set($key, $this->serializeValue($value), TTL::secondsLeft($ttl));
    }

    protected function storeMultipleValues(array $values, DateInterval|int|null $ttl): bool
    {
        return $this->memcached->setMulti($values, TTL::secondsLeft($ttl));
    }

    protected function removeValue(string $key): bool
    {
        return $this->memcached->delete($key);
    }

    protected function removeMultipleValues(array $keys): bool
    {
        $this->memcached->deleteMulti($keys);
        return true;
    }

    protected function flush(): bool
    {
        return $this->memcached->flush();
    }
}