<?php

namespace SSF\ORM\Object\Storage;

interface StorageInterface
{
    public function create(array|object $source): int|bool;

    public function extractFields(object $source): array;

    public function find(mixed $index): object;

    public function save(object $source): int|bool;
}