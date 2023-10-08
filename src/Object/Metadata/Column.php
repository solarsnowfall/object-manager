<?php

namespace SSF\ORM\Object\Metadata;

use Attribute;

#[Attribute]
class Column implements MetadataAttribute, PropertyAttribute
{
    public function __construct(
        public ?string $name = null,
        public ?string $key = null,
        public ?string $default = null,
        public bool $nullable = false,
        public ?DataType $dataType = null,
        public ?int $maxLength = null,
        public ?int $precision = null,
        public ?int $scale = null,
        public ?string $extra = null
    ) {}
}