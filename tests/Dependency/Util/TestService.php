<?php

namespace SSF\ORM\Tests\Dependency\Util;

class TestService
{
    public function __construct(
        public TestDependency $dependency
    ){}
}