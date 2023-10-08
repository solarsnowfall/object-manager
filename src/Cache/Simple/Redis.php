<?php

namespace SSF\ORM\Cache\Simple;

use DateInterval;
use RedisException;
use SSF\ORM\Cache\Old\TTL;

class Redis extends AbstractAdapter
{
    public function __construct(
        private \Redis $redis
    ){}

    protected function hasKey(string $key): bool
    {
        try {
            return (bool) $this->redis->exists($key);
        } catch (RedisException $exception) {
            return false;
        }
    }

    protected function keyExpired(string $key): bool
    {
        return $this->hasKey($key);
    }

    protected function fetchValue(string $key): mixed
    {
        return $this->redis->get($key);
    }

    protected function fetchFoundValues(array $keys): array
    {
        return $this->redis->mGet($keys);
    }

    protected function storeValue(string $key, mixed $value, DateInterval|int|null $ttl): bool
    {
        return $this->redis->setex($key, TTL::secondsLeft($ttl), $value);
    }

    protected function storeMultipleValues(array $values, DateInterval|int|null $ttl): bool
    {
        $seconds = TTL::secondsLeft($ttl);

        if ($seconds < 0) {
            return false;
        }

        $this->redis->multi();

        $stored = true;
        foreach ($values as $key => $value) {
            $value = $this->serializeValue($value);
            if (!$this->setValue($key, $value, $ttl)) {
                $stored = false;
            }
        }

        return $this->redis->exec() && $stored;
    }

    protected function removeValue(string $key): bool
    {
        return $this->redis->del($key);
    }

    protected function removeMultipleValues(array $keys): bool
    {
        return $this->redis->del(...$keys);
    }

    protected function flush(): bool
    {
        return $this->redis->flushDB();
    }

    private function setValue(string $key, mixed $value, DateInterval|int|null $ttl): bool
    {
        $seconds = TTL::secondsLeft($ttl);

        if ($seconds < 0) {
            return false;
        }

        $value = $this->serializeValue($value);
        return $seconds ? $this->redis->setex($key, $seconds, $value) : $this->redis->set($key, $value);
    }
}