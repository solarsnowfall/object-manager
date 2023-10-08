<?php

namespace SSF\ORM\Object\Metadata;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class ClassMetadata
{
    private readonly ReflectionClass $class;

    private static array $classAttributes = [];

    /**
     * @var array
     */
    private static array $propertyAttributes = [];

    /**
     * @param ReflectionClass $class
     */
    public function __construct(object|string $objectOrClass)
    {
        $this->class = $this->getReflector($objectOrClass);
    }

    /**
     * @param string|null $type
     * @return ClassAttribute[]
     */
    public function getClassAttributes(?string $type = null): array
    {
        $className = $this->class->getName();

        if (!isset(static::$classAttributes[$className])) {
            foreach ($this->class->getAttributes() as $attribute) {
                if ($this->isMetadataAttribute($attribute)) {
                    static::$classAttributes[$className][$attribute->getName()] = $attribute;
                }
            }
        }

        return $type ? static::$classAttributes[$className][$type] : static::$classAttributes[$className];
    }

    /**
     * @return PropertyAttribute[]
     */
    public function getPropertyAttributes(?string $type = null): array
    {
        $className = $this->class->getName();

        if (!isset(static::$propertyAttributes[$className])) {
            foreach ($this->class->getProperties() as $property) {
                static::$propertyAttributes[$className][$property->getName()] =
                    $this->getPropertyMetadataAttributes($property);
            }
        }

        return $type ? static::$propertyAttributes[$className][$type] : static::$propertyAttributes[$className];
    }

    /**
     * @return Column|null
     */
    public function getIndex(): ?Column
    {
        foreach ($this->getPropertyAttributes() as $attribute) {
            if ($attribute instanceof Column && $attribute->key === 'PRI') {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * @param mixed $object
     * @return array
     */
    public function getColumns(mixed $object): array
    {
        $columns = [];
        foreach ($this->class->getProperties() as $property) {
            foreach ($this->getPropertyMetadataAttributes($property) as $attribute) {
                if ($attribute instanceof Column) {
                    $columns[$property->getName()] = $property->getValue($object);
                }
            }
        }

        return $columns;
    }

    /**
     * @param ReflectionProperty $property
     * @return array
     */
    private function getPropertyMetadataAttributes(ReflectionProperty $property): array
    {
        $attributes = [];
        foreach ($property->getAttributes() as $attribute) {
            if ($this->isMetadataAttribute($attribute)) {
                $attributes[$attribute->getName()][] = $attribute->newInstance();
            }
        }

        return $attributes;
    }

    /**
     * @param ReflectionAttribute $attribute
     * @return bool
     */
    private function isMetadataAttribute(ReflectionAttribute $attribute): bool
    {
        try {
            return (new ReflectionClass($attribute->getName()))->implementsInterface(MetadataAttribute::class);
        } catch (ReflectionException $exception) {
            return false;
        }
    }
}