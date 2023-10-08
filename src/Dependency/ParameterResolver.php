<?php

namespace SSF\ORM\Dependency;

use Psr\Container\ContainerInterface;
use ReflectionNamedType;
use ReflectionParameter;

class ParameterResolver
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ReflectionParameter $parameter
    ){}

    public function resolveInstance(): mixed
    {
        $types = $this->getTypes();
        return $this->findClass($types)
            ?? $this->findInterface($types)
            ?? $this->findChildClass($types)
            ?? null;
    }

    /**
     * @return ReflectionNamedType[]
     */
    private function getTypes(): array
    {
        $type = $this->parameter->getType();
        return false === $type instanceof ReflectionNamedType
            ? $type->getTypes()
            : [$type];
    }

    /**
     * @param ReflectionNamedType[] $types
     * @return mixed
     */
    private function findClass(array $types): mixed
    {
        foreach ($types as $type) {
            if (class_exists($type->getName()) && $this->container->has($type->getName())) {
                return $this->container->get($type->getName());
            }
        }

        return null;
    }

    /**
     * @param ReflectionNamedType[] $types
     * @return mixed
     */
    private function findInterface(array $types): mixed
    {
        foreach ($types as $type) {
            if (!interface_exists($type->getName())) {
                continue;
            }
            foreach (get_declared_classes() as $class) {
                if ($this->container->has($class) && in_array($type->getName(), class_implements($class))) {
                    return $this->container->get($class);
                }
            }
        }

        return null;
    }

    /**
     * @param array $types
     * @return mixed
     */
    private function findChildClass(array $types): mixed
    {
        foreach ($types as $type) {
            if (!class_exists($type->getName())) {
                continue;
            }
            foreach (get_declared_classes() as $class) {
                if ($this->container->has($class) && is_subclass_of($class, $type->getName())) {
                    return $this->container->get($class);
                }
            }
        }

        return null;
    }
}