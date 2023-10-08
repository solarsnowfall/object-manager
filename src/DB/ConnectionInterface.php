<?php

namespace SSF\ORM\DB;

interface ConnectionInterface
{
    public function affectedRows(): int;

    public function connect(): void;

    public function errorCode(): int;

    public function errorMessage(): string;

    public function execute(string $query, array $parameters = null, string|array $types = null): StatementInterface;

    public function lastInsertId(): int;

    public function prepare(string $query, array $parameters = null, string|array $types = null): StatementInterface;
}