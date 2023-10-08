<?php

namespace SSF\ORM\Tests\Dependency\Util;

class TestServiceWithInterface
{
    public function __construct(
        public TestDependencyInterface $dependency
    ){}
}