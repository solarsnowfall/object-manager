<?php

namespace SSF\ORM\Cache\Old\Pool;

use DateInterval;
use SSF\ORM\Cache\Old\TTL;

class ArrayCachePool extends AbstractCachePool
{
    public function __construct(
        private array $values = [],
        private array $expirations = []
    ) {
        if ($count = count($values)) {
            if (count($expirations) < $count) {
                $this->expirations = $expirations + array_fill_keys(array_diff_key($values, $expirations), null);
            }
        }
    }

    protected function deleteItemsFromPool(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->deleteSingleItemFromPool($key);
        }

        return true;
    }

    protected function deleteSingleItemFromPool(string $key): bool
    {
        unset($this->values[$key], $this->expirations[$key]);
        return true;
    }

    protected function flushPool(): bool
    {
        $this->values = $this->expirations = [];
        return true;
    }

    protected function getItemsFromPool(array $keys): array
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getSingleItemFromPool($key);
        }

        return $items;
    }

    protected function getSingleItemFromPool(string $key): \SSF\ORM\Cache\Old\Pool\Item\CacheItemInterface
    {
         return new CacheItem($key, unserialize($this->values[$key] ?? null));
    }

    protected function poolHasItem(string $key): bool
    {
        return isset($this->values[$key]);
    }

    protected function poolItemIsHit(string $key): bool
    {
        return isset($this->expirations[$key]) && time() < $this->expirations[$key];
    }

    protected function storeItemInPool(\SSF\ORM\Cache\Old\Pool\Item\CacheItemInterface $item, DateInterval|int|null $ttl = null): bool
    {
        $this->values[$item->getKey()] = serialize($item->get());
        $this->expirations[$item->getKey()] = TTL::timestamp($ttl);
        return true;
    }
}