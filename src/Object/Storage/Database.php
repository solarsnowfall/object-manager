<?php

namespace SSF\ORM\Object\Storage;

use SSF\ORM\DB\AdapterInterface;
use SSF\ORM\Object\Manager;

class Database implements StorageInterface
{
    public function __construct(
        private readonly Manager $manager,
        private readonly AdapterInterface $database
    ){}


    public function create(object|array $source): int|bool
    {
        // TODO: Implement create() method.
    }

    public function extractFields(object $source): array
    {
        // TODO: Implement extractFields() method.
    }

    public function find(mixed $index): object
    {
        // TODO: Implement find() method.
    }

    public function save(object $source): int|bool
    {
        // TODO: Implement save() method.
    }
}