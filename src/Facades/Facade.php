<?php

namespace SSF\ORM\Facades;

use SSF\ORM\Dependency\Container;

abstract class Facade
{
    private static ?Container $container = null;

    private static array $instances = [];

    abstract protected static function instanceKey(): string;

    private static function resolveInstance(): mixed
    {
        $key = static::instanceKey();

        if (!isset(static::$instances[$key])) {
            static::$instances[$key] = static::newInstance($key);
        }

        return static::$instances[$key];
    }

    private static function newInstance(string $key): mixed
    {
        if (null === static::$container) {
            static::$container = Container::getContainer();
        }

        return static::$container->has($key)
            ? static::$container->get($key)
            : static::$container->make($key);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return self::resolveInstance()->$name(...$arguments);
    }
}