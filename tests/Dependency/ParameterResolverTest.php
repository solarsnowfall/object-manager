<?php

namespace Dependency;

use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use SSF\ORM\Dependency\Container;
use SSF\ORM\Dependency\ParameterResolver;
use SSF\ORM\Tests\Dependency\Util\TestDependency;
use SSF\ORM\Tests\Dependency\Util\TestService;
use SSF\ORM\Tests\Dependency\Util\TestServiceWithAbstract;
use SSF\ORM\Tests\Dependency\Util\TestServiceWithInterface;

class ParameterResolverTest extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testResolvesClass()
    {
        $this->container->set(TestService::class);
        $this->container->set(TestDependency::class);

        $resolver = new ParameterResolver($this->container, new ReflectionParameter(
            [TestService::class, '__construct'], 'dependency'
        ));
        $this->assertInstanceOf(TestDependency::class, $resolver->resolveInstance());
    }

    public function testResolvesInterface()
    {
        $this->container->set(TestServiceWithInterface::class);
        $this->container->set(TestDependency::class);

        $resolver = new ParameterResolver($this->container, new ReflectionParameter(
            [TestService::class, '__construct'], 'dependency'
        ));
        $this->assertInstanceOf(TestDependency::class, $resolver->resolveInstance());
    }

    public function testResolvesChildClass()
    {
        $this->container->set(TestServiceWithAbstract::class);
        $this->container->set(TestDependency::class);

        $resolver = new ParameterResolver($this->container, new ReflectionParameter(
            [TestService::class, '__construct'], 'dependency'
        ));
        $this->assertInstanceOf(TestDependency::class, $resolver->resolveInstance());
    }
}