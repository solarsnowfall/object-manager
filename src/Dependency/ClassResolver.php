<?php

namespace SSF\ORM\Dependency;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;
use SSF\ORM\Util\Arr;

class ClassResolver
{
    /**
     * @param ContainerInterface $container
     * @param ReflectionClass $class
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ReflectionClass $class
    ){}

    /**
     * @param array $arguments
     * @return mixed
     */
    public function createInstance(array $arguments = []): mixed
    {
        try {
            return $this->class->newInstance(...$this->getDependencies($arguments));
        } catch (ReflectionException $exception) {
            throw new RuntimeException(
                message: sprintf("Failed to create instance of %s", $this->class->getName()),
                previous: $exception
            );
        }
    }

    /**
     * @param array $arguments
     * @return array
     */
    private function getDependencies(array $arguments): array
    {
        $constructor = $this->class->getConstructor();

        if (null === $constructor) {
            return [];
        }

        return $this->resolveDependencies($constructor, $arguments);
    }

    /**
     * @param ReflectionMethod $constructor
     * @param array $arguments
     * @return array
     */
    private function resolveDependencies(ReflectionMethod $constructor, array $arguments): array
    {
        $sequential = Arr::isSequential($arguments);
        $dependencies = [];

        foreach ($constructor->getParameters() as $key => $parameter) {
            if ($sequential && isset($arguments[$key])) {
                $dependencies[] = $arguments[$key];
            } elseif (!$sequential && isset($arguments[$parameter->getName()])) {
                $dependencies[] = $arguments[$parameter->getName()];
            } elseif (null !== $instance = $this->resolveParameter($parameter)) {
                $dependencies[] = $instance;
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            }
        }

        return $dependencies;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed
     */
    private function resolveParameter(ReflectionParameter $parameter): mixed
    {
        return (new ParameterResolver($this->container, $parameter))->resolveInstance();
    }
}