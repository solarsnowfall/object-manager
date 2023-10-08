<?php

namespace SSF\ORM\Tests\Dependency\Util;

class TestServiceWithAbstract
{
    public function __construct(
        public TestDependencyAbstract $dependency
    ){}
}