<?php

namespace SSF\ORM\Cache\Old;

use DateInterval;
use DateTime;

trait ConvertsExpirations
{
    private function ttlToExpirationTimestamp(DateInterval|int|null $ttl): ?int
    {
        if (null === $ttl) {
            return null;
        }

        if ($ttl instanceof DateInterval) {
            return (new DateTime())->add($ttl)->getTimestamp();
        }

        return time() + $ttl;
    }

    private function ttlToInt(DateInterval|int|null $ttl = null): ?int
    {
        if (null === $ttl) {
            return null;
        }

        if ($ttl instanceof DateInterval) {
            return (new DateTime())->add($ttl)->getTimestamp();
        }

        return time() + $ttl;
    }
}