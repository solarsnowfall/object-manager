<?php

namespace SSF\ORM\Dependency;

use Closure;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;

class Container implements ContainerInterface
{
    private static ?Container $container = null;


    /**
     * @var array
     */
    private array $definitions = [];

    /**
     * @var array
     */
    private array $instances = [];

    /**
     * @var array
     */
    private array $singletons = [];

    /**
     *
     */
    public function __construct()
    {
        $this->set(ContainerInterface::class, $this);
        static::$container = $this;
    }

    /**
     * @return Container
     */
    public static function getContainer(): Container
    {
        if (null === static::$container) {
            static::$container = new self();
        }

        return static::$container;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Class not found: $id");
        }

        if ($this->hasInstance($id)) {
            return $this->instances[$id];
        }

        if ($this->isSingleton($id)) {
            return $this->instances[$id] = $this->resolveInstance($id, $this->definitions[$id]);
        }

        return $this->resolveInstance($id, $this->definitions[$id]);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions);
    }

    /**
     * @return array
     */
    public function definitions(): array
    {
        return $this->definitions;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function isSingleton(string $id): bool
    {
        return isset($this->singletons[$id]);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function hasInstance(string $id): mixed
    {
        return isset($this->instances[$id]);
    }

    /**
     * @param string $id
     * @param mixed $definition
     * @param bool $singleton
     * @return void
     */
    public function set(string $id, mixed $definition = null, bool $singleton = false): void
    {
        $this->definitions[$id] = $definition;

        if ($singleton) {
            $this->singletons[$id] = true;
        }
    }

    /**
     * @param string $id
     * @param mixed $definition
     * @return void
     */
    public function singleton(string $id, mixed $definition): void
    {
        $this->set($id, $definition, true);
    }

    /**
     * @param string $id
     * @return void
     */
    public function forget(string $id): void
    {
        unset($this->definitions[$id], $this->singletons[$id], $this->instances[$id]);
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->definitions = $this->singletons = $this->instances = [];
    }

    /**
     * @param string $class
     * @param array $arguments
     * @return mixed
     */
    public function make(string $class, array $arguments = []): mixed
    {
        try {
            return (new ClassResolver($this, new ReflectionClass($class)))->createInstance($arguments);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(
                message: "Unable to create instance for $class",
                previous: $exception
            );
        }
    }

    /**
     * @param string $id
     * @param mixed $definition
     * @return mixed
     */
    private function resolveInstance(string $id, mixed $definition): mixed
    {
        if ($definition instanceof Closure) {
            return $definition($this);
        }

        if (class_exists($id) && is_array($definition) || is_null($definition)) {
            return $this->make($id, $definition ?? []);
        }

        return $definition;
    }
}