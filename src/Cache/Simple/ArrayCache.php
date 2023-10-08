<?php

namespace SSF\ORM\Cache\Simple;

use DateInterval;
use SSF\ORM\Cache\Old\TTL;

class ArrayCache extends AbstractAdapter
{
    private array $cache = [];

    private array $expiry = [];

    public function clear(): bool
    {
        $this->cache = $this->expiry = [];
        return true;
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ((array) $values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ((array) $keys as $key) {
            $this->validateKey($key);
            $this->removeValue($key);
        }

        return true;
    }

    protected function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->cache);
    }

    protected function keyExpired(string $key): bool
    {
        return 0 !== $this->expiry[$key] && time() - $this->expiry[$key] > 0;
    }

    protected function fetchValue(string $key): mixed
    {
        return $this->deserializeValue($this->cache[$key]);
    }

    protected function fetchFoundValues(array $keys): array
    {
        $values = [];
        foreach ($keys as $key) {
            if ($this->hasKey($key)) {
                $values[$key] = $this->deserializeValue($this->cache[$key]);
            }
        }

        return $values;
    }

    protected function storeValue(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $seconds = TTL::secondsLeft($ttl);

        if ($seconds <= 0) {
            return false;
        }

        $this->cache[$key] = $this->serializeValue($value);
        $this->expiry[$key] = time() + $seconds;
        return true;
    }

    protected function storeMultipleValues(array $values, DateInterval|int|null $ttl): bool
    {
        $seconds = TTL::secondsLeft($ttl);

        if ($values <= 0) {
            return false;
        }

        $time = time();
        foreach ($values as $key => $value) {
            $this->cache[$key] = $this->serializeValue($value);
            $this->expiry[$key] = $time + $seconds;
        }

        return true;
    }

    protected function removeValue(string $key): bool
    {
        if (!array_key_exists($key, $this->cache)) {
            return false;
        }

        unset($this->cache[$key], $this->expiry[$key]);
        return true;
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
        $this->cache = $this->expiry = [];
        return true;
    }
}