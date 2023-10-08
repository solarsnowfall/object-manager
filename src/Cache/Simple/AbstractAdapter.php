<?php

namespace SSF\ORM\Cache\Simple;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use SSF\ORM\Cache\Exception\InvalidCacheKeyException;
use SSF\ORM\Cache\ValidatesKeys;

abstract class AbstractAdapter implements CacheInterface
{
    use ValidatesKeys;

    protected abstract function hasKey(string $key): bool;

    protected abstract function keyExpired(string $key): bool;

    protected abstract function fetchValue(string $key): mixed;

    protected abstract function fetchFoundValues(array $keys): array;

    protected abstract function storeValue(string $key, mixed $value, DateInterval|int|null $ttl): bool;

    protected abstract function storeMultipleValues(array $values, DateInterval|int|null $ttl): bool;

    protected abstract function removeValue(string $key): bool;

    protected abstract function removeMultipleValues(array $keys): bool;

    protected abstract function flush(): bool;

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->fetchValue($key) : $default;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->validateKey($key);
        return $this->storeValue($key, $value, $ttl);
    }

    public function delete(string $key): bool
    {
        $this->validateKey($key);
        return $this->removeValue($key);
    }

    public function clear(): bool
    {
        return $this->flush();
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $keys = (array) $keys;
        $this->validateKeys($keys);

        $found = $this->fetchFoundValues($keys);
        if (count($keys) === count($found)) {
            return $found;
        }

        return $found + array_fill_keys($keys, $default);
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        $values = (array) $values;
        $this->validateKeys(array_keys($values));
        return $this->storeMultipleValues($values, $ttl);
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $this->validateKeys($keys);
        return $this->removeMultipleValues((array) $keys);
    }

    public function has(string $key): bool
    {
        return $this->hasKey($key) && !$this->keyExpired($key);
    }

    protected function serializeValue(mixed $value): mixed
    {
        return serialize($value);
    }

    protected function deserializeValue(mixed $serializedValue): mixed
    {
        return unserialize($serializedValue);
    }
}