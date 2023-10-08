<?php

namespace SSF\ORM\Cache\Simple;

use DateInterval;
use SSF\ORM\Cache\Old\TTL;

class Apcu extends AbstractAdapter
{
    protected function hasKey(string $key): bool
    {
        return apcu_exists($key);
    }

    protected function keyExpired(string $key): bool
    {
        apcu_fetch($key, $success);
        return $success;
    }

    protected function fetchValue(string $key): mixed
    {
        return apcu_fetch($key);
    }

    protected function fetchFoundValues(array $keys): array
    {
        return apcu_fetch($keys);
    }

    protected function storeValue(string $key, mixed $value, DateInterval|int|null $ttl): bool
    {
        return apcu_store($key, $value, TTL::secondsLeft($ttl));
    }

    protected function storeMultipleValues(array $values, DateInterval|int|null $ttl): bool
    {
        return apcu_store($values, null, TTL::secondsLeft($ttl));
    }

    protected function removeValue(string $key): bool
    {
        return apcu_delete($key);
    }

    protected function removeMultipleValues(array $keys): bool
    {
        return apcu_delete($keys);
    }

    protected function flush(): bool
    {
        return apcu_clear_cache();
    }
}