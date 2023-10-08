<?php

namespace Dependency;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SSF\ORM\Dependency\ClassResolver;
use SSF\ORM\Dependency\Container;
use SSF\ORM\Tests\Dependency\Util\TestDependency;
use SSF\ORM\Tests\Dependency\Util\TestService;
use SSF\ORM\Tests\Dependency\Util\TestServiceWithAbstract;

class ClassResolverTest extends TestCase
{
    protected Container $container;
    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testResolveClass()
    {
        $this->container->set(TestService::class);
        $this->container->set(TestDependency::class);
        $resolver = new ClassResolver($this->container, new ReflectionClass(TestService::class));
        $this->assertInstanceOf(TestService::class, $resolver->createInstance());
    }
}