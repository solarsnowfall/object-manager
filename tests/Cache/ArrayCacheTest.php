<?php

namespace SSF\ORM\Tests\Cache;

use Psr\SimpleCache\CacheInterface;
use SSF\ORM\Cache\Simple\ArrayCache;

class ArrayCacheTest extends AbstractCacheTest
{

    protected static function serviceAvailable(string $service): bool
    {
        return class_exists(ArrayCache::class);
    }


    protected static function initializeService(): CacheInterface
    {
        return new ArrayCache;
    }
}