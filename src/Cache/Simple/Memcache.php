<?php

namespace SSF\ORM\Cache\Simple;

use DateInterval;
use SSF\ORM\Cache\Old\TTL;

class Memcache extends AbstractAdapter
{
    public function __construct(
        private \Memcache $memcache
    ){}

    protected function hasKey(string $key): bool
    {
        return false !== $this->memcache->get($key);
    }

    protected function keyExpired(string $key): bool
    {
        return false !== $this->memcache->get($key);
    }

    protected function fetchValue(string $key): mixed
    {
        return $this->memcache->get($key);
    }

    protected function fetchFoundValues(array $keys): array
    {
        $values = [];
        foreach ($keys as $key) {
            if ($value = $this->fetchValue($key)) {
                $values[$key] = $value;
            }
        }

        return $values;
    }

    protected function storeValue(string $key, mixed $value, DateInterval|int|null $ttl): bool
    {
        return $this->memcache->set(
            key: $key,
            var: $this->serializeValue($value),
            flag: MEMCACHE_COMPRESSED,
            expire: TTL::secondsLeft($ttl)
        );
    }

    protected function storeMultipleValues(array $values, DateInterval|int|null $ttl): bool
    {
        $stored = true;
        $seconds = TTL::secondsLeft($ttl);
        foreach ($values as $key => $value) {
            if (
                !$this->memcache->set(
                    key: $key,
                    var: $value,
                    flag: MEMCACHE_COMPRESSED,
                    expire: $seconds
                )
            ) {
                $stored = false;
            }
        }

        return $stored;
    }

    protected function removeValue(string $key): bool
    {
        return $this->memcache->delete($key);
    }

    protected function removeMultipleValues(array $keys): bool
    {
        $removed = true;
        foreach ($keys as $key) {
            if (!$this->removeValue($key)) {
                $removed = false;
            }
        }

        return $removed;
    }

    protected function flush(): bool
    {
        return $this->memcache->flush();
    }
}