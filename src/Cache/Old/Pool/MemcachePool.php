<?php

namespace SSF\ORM\Cache\Old\Pool;

use DateInterval;
use Memcache;
use SSF\ORM\Cache\Old\Pool\Item\CacheItemInterface;

class MemcachePool extends AbstractCachePool
{
    public function __construct(
        private Memcache $memcache
    ){}

    protected function deleteItemsFromPool(array $keys): bool
    {
        $deleted = true;
        foreach ($keys as $key) {
            if (false === $this->deleteSingleItemFromPool($key)) {
                $deleted = false;
            }
        }

        return $deleted;
    }

    protected function deleteSingleItemFromPool(string $key): bool
    {
        return $this->memcache->delete($key);
    }

    protected function flushPool(): bool
    {
        return $this->memcache->flush();
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
        return new CacheItem($key, $this->memcache->get($key) ?: null);
    }

    protected function poolHasItem(string $key): bool
    {
        return $this->memcache->get($key) !== false;
    }

    protected function poolItemIsHit(string $key): bool
    {
        return $this->memcache->get($key) !== false;
    }

    protected function storeItemInPool(CacheItemInterface $item, DateInterval|int|null $ttl = null): bool
    {
        return $this->memcache->set(
            $item->getKey(),
            $item->get(),
            MEMCACHE_COMPRESSED,
            $item->getTtl()->getSecondsLeft()
        );
    }
}