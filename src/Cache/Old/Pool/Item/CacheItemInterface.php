<?php

namespace SSF\ORM\Cache\Old\Pool\Item;

use Psr\Cache\CacheItemInterface as PsrCacheItemInterface;
use SSF\ORM\Cache\Old\TTL;

interface CacheItemInterface extends PsrCacheItemInterface
{
    public function getTtl(): TTL;
}