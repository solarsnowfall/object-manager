<?php

namespace SSF\ORM\Object;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;

class ClassInspector
{
    /**
     * @param object|string $objectOrClass
     * @return ReflectionClass
     */
    public static function getClass(object|string $objectOrClass): ReflectionClass
    {
        try {
            return new ReflectionClass($objectOrClass);
        } catch (ReflectionException $exception) {
            $class = is_string($objectOrClass) ? $objectOrClass : get_class($objectOrClass);
            throw new RuntimeException(message: "Class not found: $class", previous: $exception);
        }
    }

    /**
     * @param object|string $objectOrClass
     * @param string|null $name
     * @param int $flags
     * @return ReflectionAttribute[]
     */
    public static function getClassAttributes(
        object|string $objectOrClass,
        ?string $name = null,
        int $flags = 0
    ): array {
        return static::getClass($objectOrClass)->getAttributes($name, $flags);
    }

    /**
     * @param object|string $objectOrClass
     * @param int|null $filter
     * @return ReflectionProperty[]
     */
    public static function getProperties(object|string $objectOrClass, ?int $filter = null): array
    {
        return static::getClass($objectOrClass)->getProperties($filter);
    }

    /**
     * @param object|string $objectOrClass
     * @param int|null $filter
     * @param string|null $name
     * @param int $flags
     * @return ReflectionAttribute[]
     */
    public static function getPropertyAttributes(
        object|string $objectOrClass,
        ?int $filter = null,
        ?string $name = null,
        int $flags = 0
    ): array {
        $attributes = [];
        foreach (static::getProperties($objectOrClass, $filter) as $property) {
            $attributes[] = $property->getAttributes($name, $flags);
        }

        return $attributes;
    }
}