<?php

namespace SSF\ORM\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

abstract class AbstractCacheTest extends TestCase
{
    abstract protected static function serviceAvailable(string $service): bool;

    abstract protected static function initializeService(): CacheInterface;

    abstract public function testHasWithMissingKey(): void;

    abstract public function testSet(): void;

    abstract public function testGetWithExpectedKey(): void;

    abstract public function testGetMissingKey(): void;



    /**
     * @before
     */
    public function skipIfServiceNotFound(string $service): void
    {
        if (! static::serviceAvailable($service)) {
            $this->markTestSkipped("$service not available");
        }
    }
}