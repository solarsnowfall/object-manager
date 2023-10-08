<?php

namespace SSF\ORM\Cache;

use SSF\ORM\Cache\Exception\InvalidCacheKeyException;

trait ValidatesKeys
{
    protected final function testKey(string $key): bool
    {
        return preg_match('/^[A-Za-z0-9_.-]+$/', $key);
    }

    protected final function validateKey(string $key): void
    {
        if (!$this->testKey($key)) {
            throw new InvalidCacheKeyException("Invalid cache key: $key");
        }
    }

    protected final function validateKeys(iterable $keys): void
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