<?php

namespace SSF\ORM\Cache\Old\Pool;

use DateInterval;
use DateTime;
use DateTimeInterface;
use SSF\ORM\Cache\Old\Pool\Item\CacheItemInterface;
use SSF\ORM\Cache\Old\TTL;

class CacheItem implements CacheItemInterface
{
    private TTL $ttl;

    public function __construct(
        private readonly string $key,
        private mixed $value = null,
        DateInterval|int|null $ttl = null
    ){
        $this->ttl = new TTL($ttl);
    }

    public function getTtl(): TTL
    {
        return $this->ttl;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return null !== $this->value && $this->ttl->getTimestamp() !== null || $this->ttl->getTimestamp() > time();
    }

    public function set(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function expiresAt(?DateTimeInterface $expiration): static
    {
        $this->expiration = $this->getExpirationTimestamp($expiration);
        return $this;
    }

    public function expiresAfter(DateInterval|int|null $time): static
    {
        $this->expiration = $this->getExpirationTimestamp($time);
        return $this;
    }

    private function getExpirationTimestamp(DateTimeInterface|DateInterval|int|null $expiration): ?int
    {
        if (null === $expiration) {
            return null;
        }

        if ($expiration instanceof DateTimeInterface) {
            return $expiration->getTimestamp();
        }

        if ($expiration instanceof DateInterval) {
            return (new DateTime())->add($expiration)->getTimestamp();
        }

        return time() + $expiration;
    }
}