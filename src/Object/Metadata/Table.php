<?php

namespace SSF\ORM\Object\Metadata;

use Attribute;

#[Attribute]
class Table implements MetadataAttribute, ClassAttribute
{
    public function __construct(
        public string $name
    ){}
}