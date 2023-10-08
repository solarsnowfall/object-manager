<?php

namespace SSF\ORM\Cache\Old;

trait ValidatesKeys
{
    private function testKey(string $key): bool
    {
        return preg_match('/^[A-Za-z0-9_.-]+$/', $key);
    }

    private function validateKey(string $key): void
    {
        if (!$this->testKey($key)) {
            throw new InvalidCacheKeyException("Invalid cache key: $key");
        }
    }

    private function validateKeys(iterable $keys): void
    {
        foreach ((array) $keys as $key) {
            $this->validateKey($key);
        }
    }

    private function validateKeysGetValues(iterable $keys, callable $callback): array
    {
        $values = [];
        foreach ((array) $keys as $key) {
            $this->validateKey($key);
            $values[$key] = call_user_func($callback, $key);
        }

        return $values;
    }
}