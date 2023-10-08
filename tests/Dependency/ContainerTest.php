<?php

namespace SSF\ORM\Tests\Dependency;

use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Test;
use SSF\ORM\Dependency\Container;
use SSF\ORM\Dependency\NotFoundException;
use SSF\ORM\Tests\Dependency\Util\TestDependency;
use SSF\ORM\Tests\Dependency\Util\TestDependencyAbstract;
use SSF\ORM\Tests\Dependency\Util\TestDependencyInterface;
use SSF\ORM\Tests\Dependency\Util\TestService;
use SSF\ORM\Tests\Dependency\Util\TestServiceWithInterface;

class ContainerTest extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testHas()
    {
        $this->container->set(TestDependency::class);
        $this->assertTrue($this->container->has(TestDependency::class));
    }

    public function testHasNot()
    {
        $this->assertFalse($this->container->has(TestDependency::class));
    }

    public function throwsExceptionWhenHasNot()
    {
        try {
            $this->container->get(TestDependency::class);
        } catch (Exception $exception) {
            $this->assertInstanceOf(NotFoundException::class, $exception);
        }
    }

    public function testGetClassWithNullDefinition()
    {
        $this->container->set(TestDependency::class);
        $this->assertInstanceOf(TestDependency::class, $this->container->get(TestDependency::class));
    }

    public function testGetClassWithObjectDefinition()
    {
        $this->container->set(TestDependency::class, new TestDependency(1, 2, 3));
        $instance = $this->container->get(TestDependency::class);
        $this->assertInstanceOf(TestDependency::class, $instance);
        $this->assertEquals(1, $instance->a);
        $this->assertEquals(2, $instance->b);
        $this->assertEquals(3, $instance->c);
    }

    public function testGetClassWithAssociativeArrayDefinition()
    {
        $this->container->set(TestDependency::class, ['a' => 1, 'b' => 2, 'c' => 3]);
        $instance = $this->container->get(TestDependency::class);
        $this->assertEquals(1, $instance->a);
        $this->assertEquals(2, $instance->b);
        $this->assertEquals(3, $instance->c);
    }

    public function testGetClassWithSequentialArrayDefinition()
    {
        $this->container->set(TestDependency::class, [1, 2, 3]);
        $instance = $this->container->get(TestDependency::class);
        $this->assertEquals(1, $instance->a);
        $this->assertEquals(2, $instance->b);
        $this->assertEquals(3, $instance->c);
    }

    public function testGetClassWithNestedDependencies()
    {
        $this->container->set(TestDependency::class);
        $this->container->set(TestService::class);
        $instance = $this->container->get(TestService::class);
        $this->assertInstanceOf(TestDependency::class, $instance->dependency);
    }
}