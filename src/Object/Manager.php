<?php

namespace SSF\ORM\Object;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use SSF\ORM\Object\Metadata\ClassMetadata;
use SSF\ORM\Object\Metadata\Column;
use SSF\ORM\Object\Storage\StorageInterface;

class Manager
{
    /**
     * @var ReflectionClass[]
     */
    private static array $reflectors = [];

    public function __construct(
        private readonly StorageInterface $storage
    ){}

    public function find(object $source)
    {
        $index = $this->getClassMetadata($source)->getIndex();
    }

    /**
     * @param object $object
     * @return bool|int
     */
    public function save(object $object): bool|int
    {
        return $this->storage->save($object);
    }

    private function getReflector(object $source)
    {
        $class = get_class($source);

        if (isset(static::$reflectors[$class])) {
            return static::$reflectors[$class];
        }

        try {
            return static::$reflectors[$class] = new ReflectionClass($source);
        } catch (ReflectionException $exception) {
            throw new RuntimeException(sprintf("Class not found: %s", get_class($source)));
        }
    }

    private function getClassMetadata(object $source): ClassMetadata
    {
        return new ClassMetadata($this->getReflector($source));
    }

    private function extractStorageData(object $source)
    {
        foreach ($this->getReflector($source)->getProperties() as $property) {

        }
    }
}