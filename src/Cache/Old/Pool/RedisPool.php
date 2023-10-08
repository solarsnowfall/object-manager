<?php

namespace SSF\ORM\Cache\Old\Pool;

use DateInterval;
use Redis;
use SSF\ORM\Cache\Old\Pool\Item\CacheItemInterface;
use SSF\ORM\Cache\Old\TTL;

class RedisPool extends AbstractCachePool
{
    public function __construct(
        private Redis $redis
    ){}

    protected function deleteItemsFromPool(array $keys): bool
    {
        return $this->redis->del($keys) >= 0;
    }

    protected function deleteSingleItemFromPool(string $key): bool
    {
        return $this->redis->del($key) >= 0;
    }

    protected function flushPool(): bool
    {
        return $this->redis->flushDB();
    }

    protected function getItemsFromPool(array $keys): array
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getSingleItemFromPool($key);
        }

        return $items;
    }

    protected function getSingleItemFromPool(string $key): CacheItemInterface
    {
        return new CacheItem($key, $this->poolItemIsHit($key) ? $this->redis->get($key) : null);
    }

    protected function poolHasItem(string $key): bool
    {
        return false !== $this->redis->get($key);
    }

    protected function poolItemIsHit(string $key): bool
    {
        return false !== $this->redis->get($key);
    }

    protected function storeItemInPool(CacheItemInterface $item, DateInterval|int|null $ttl = null): bool
    {
        $secondsLeft = TTl::secondsLeft($ttl);

        if ($secondsLeft === null || $secondsLeft === 0) {
            return $this->redis->set($item->getKey(), $this->serializedItemValue($item));
        }

        return $this->redis->setex($item->getKey(), $secondsLeft, $this->serializedItemValue($item));
    }
}