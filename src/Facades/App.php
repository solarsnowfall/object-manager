<?php

namespace SSF\ORM\Facades;

use SSF\ORM\Dependency\Container;

/**
 * @method static mixed get(string $id)
 * @method static bool has(string $id)
 * @method static void set(string $id, mixed $definition = null, bool $singleton = false)
 * @method static void singleton(string $id, mixed $definition = null)
 * @method static void forget(string $id)
 * @method static bool flush()
 * @method static mixed make(string $class, array $arguments = [])
 */
class App extends Facade
{
    protected static function instanceKey(): string
    {
        return Container::class;
    }
}