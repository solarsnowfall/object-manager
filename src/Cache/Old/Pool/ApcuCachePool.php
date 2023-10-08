<?php

namespace SSF\ORM\Cache\Old\Pool;

use DateInterval;
use SSF\ORM\Cache\Old\Pool\Item\CacheItemInterface;

class ApcuCachePool extends AbstractCachePool
{

    protected function deleteItemsFromPool(array $keys): bool
    {
        return apcu_delete($keys);
    }

    protected function deleteSingleItemFromPool(string $key): bool
    {
        return $this->deleteItemsFromPool((array) $key);
    }

    protected function flushPool(): bool
    {
        return apcu_clear_cache();
    }

    protected function getItemsFromPool(array $keys): array
    {
        $items = [];
        $values = apcu_fetch($keys);
        foreach ($keys as $key) {
            $items[$key] = new CacheItem($key, $values[$key] ?: null);
        }

        return $items;
    }

    protected function getSingleItemFromPool(string $key): CacheItem
    {
        $value = apcu_fetch($key, $success);
        return new CacheItem($key, $success ? $value : null);
    }

    protected function poolHasItem(string $key): bool
    {
        return apcu_exists($key);
    }

    protected function poolItemIsHit(string $key): bool
    {
        apcu_fetch($key, $success);
        return $success;
    }

    protected function storeItemInPool(CacheItemInterface $item, DateInterval|int|null $ttl = null): bool
    {
        return apcu_store($item->getKey(), $item->get(), $item->getTtl()->getSecondsLeft());
    }
}