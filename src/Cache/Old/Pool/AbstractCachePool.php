<?php

namespace SSF\ORM\Cache\Old\Pool;

use Psr\Cache\CacheItemInterface as PsrCacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use SSF\ORM\Cache\Old\Pool\Item\CacheItemInterface;
use SSF\ORM\Cache\Old\ValidatesKeys;

abstract class AbstractCachePool implements CacheItemPoolInterface
{
    use ValidatesKeys;

    /**
     * @var CacheItemInterface[]
     */
    private array $deferredItems = [];

    protected abstract function deleteItemsFromPool(array $keys): bool;

    protected abstract function deleteSingleItemFromPool(string $key): bool;

    protected abstract function flushPool(): bool;

    protected abstract function getItemsFromPool(array $keys): array;

    protected abstract function getSingleItemFromPool(string $key): CacheItemInterface;

    protected abstract function poolHasItem(string $key): bool;

    protected abstract function poolItemIsHit(string $key): bool;

    protected abstract function storeItemInPool(CacheItemInterface $item, \DateInterval|int|null $ttl = null): bool;

    public function getItem(string $key): CacheItemInterface
    {
        $this->validateKey($key);
        return $this->hasItem($key)
           ? $this->getSingleItemFromPool($key)
           : new CacheItem($key);
    }

    public function getItems(array $keys = []): iterable
    {
        $this->validateKeys($keys);
        return $this->getItemsFromPool($keys);
    }

    public function hasItem(string $key): bool
    {
        return $this->poolHasItem($key) && $this->poolItemIsHit($key);
    }

    public function clear(): bool
    {
        $this->deferredItems = [];
        return $this->flushPool();
    }

    public function deleteItem(string $key): bool
    {
        $this->validateKey($key);
        return $this->deleteSingleItemFromPool($key);
    }

    public function deleteItems(array $keys): bool
    {
        $this->validateKeys($keys);
        return $this->deleteItemsFromPool($keys);
    }

    public function save(PsrCacheItemInterface $item): bool
    {
        if (null !== $expiration = $item->getTtl()->getTimestamp()) {
            $ttl = $expiration - time();
            if ($ttl < 0) {
                return $this->deleteSingleItemFromPool($item->getKey());
            }
        }

        return $this->storeItemInPool($item, $item->getTtl()->getTimestamp());
    }

    public function saveDeferred(PsrCacheItemInterface $item): bool
    {
        $this->deferredItems[$item->getKey()] = $item;
        return true;
    }

    public function commit(): bool
    {
        $saved = true;
        foreach ($this->deferredItems as $item) {
            if (!$this->save($item)) {
                $saved = false;
            }
        }

        $this->deferredItems = [];
        return $saved;
    }

    protected function serializedItemValue(CacheItem $item)
    {
        return serialize($item->get());
    }
}