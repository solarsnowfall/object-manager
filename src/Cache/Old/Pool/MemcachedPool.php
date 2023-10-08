<?php

namespace SSF\ORM\Cache\Old\Pool;

use DateInterval;
use Memcached;
use SSF\ORM\Cache\Old\Pool\Item\CacheItemInterface;

class MemcachedPool extends AbstractCachePool
{
    public function __construct(
        private Memcached $memcached
    ){}

    protected function deleteItemsFromPool(array $keys): bool
    {
        return !in_array(Memcached::RES_NOTFOUND, $this->memcached->deleteMulti($keys));
    }

    protected function deleteSingleItemFromPool(string $key): bool
    {
        return $this->memcached->delete($key);
    }

    protected function flushPool(): bool
    {
        return $this->memcached->flush();
    }

    protected function getItemsFromPool(array $keys): array
    {
        $items = [];
        $values = $this->memcached->getMulti($keys);
        foreach ($keys as $key) {
            $items[$key] = new CacheItem($key, $values[$key] ?? null);
        }

        return $items;
    }

    protected function getSingleItemFromPool(string $key): CacheItem
    {
        return new CacheItem($key, $this->memcached->get($key) ?: null);
    }

    protected function poolHasItem(string $key): bool
    {
        return false !== $this->memcached->get($key);
    }

    protected function poolItemIsHit(string $key): bool
    {
        return $this->poolHasItem($key);
    }

    protected function storeItemInPool(CacheItemInterface $item, DateInterval|int|null $ttl = null): bool
    {
        return $this->memcached->set($item->getKey(), $item->get(), $item->getTtl()->getSecondsLeft());
    }
}