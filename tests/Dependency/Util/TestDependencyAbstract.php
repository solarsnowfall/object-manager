<?php

namespace SSF\ORM\Tests\Dependency\Util;

abstract class TestDependencyAbstract
{
    public function __construct(
        public ?int $a = null,
        public ?int $b = null,
        public ?int $c = null
    ){}
}