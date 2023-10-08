<?php

namespace SSF\ORM\Cache\Old\Pool;

use Predis\ClientInterface;
use SSF\ORM\Cache\Old\Pool\Item\CacheItemInterface;

class PredisPool extends AbstractCachePool
{
    public function __construct(
        private ClientInterface $client
    ) {}


    protected function deleteItemsFromPool(array $keys): bool
    {
        return $this->client->del($keys);
    }

    protected function deleteSingleItemFromPool(string $key): bool
    {
        return $this->client->del($key);
    }

    protected function flushPool(): bool
    {
        return $this->client->flushdb();
    }

    protected function getItemsFromPool(array $keys): array
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = new CacheItem()
        }
    }

    protected function getSingleItemFromPool(string $key): CacheItemInterface
    {
        return new CacheItem($key, $this->client->get($key) ?: null);
    }

    protected function poolHasItem(string $key): bool
    {
        // TODO: Implement poolHasItem() method.
    }

    protected function poolItemIsHit(string $key): bool
    {
        // TODO: Implement poolItemIsHit() method.
    }

    protected function storeItemInPool(CacheItemInterface $item, \DateInterval|int|null $ttl = null): bool
    {
        // TODO: Implement storeItemInPool() method.
    }
}