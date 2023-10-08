<?php

namespace SSF\ORM\Cache\Old;

use InvalidArgumentException;
use Psr\Cache\InvalidArgumentException as InvalidCacheItemKeyExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException as InvalidCacheKeyExceptionInterface;

class InvalidCacheKeyException
    extends InvalidArgumentException
    implements InvalidCacheKeyExceptionInterface, InvalidCacheItemKeyExceptionInterface
{}