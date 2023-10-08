<?php

namespace SSF\ORM\DB;

interface AdapterInterface
{
    public function affectedRows(): int;

    public function delete(string $query, array $parameters, string|array $types = null): int;

    public function errorCode(): int;

    public function errorMessage(): string;

    public function execute(string $query, array $parameters = null, string|array $types = null): StatementInterface;

    public function fetchAll(string $query, array $parameters = null, string|array $types = null): array;

    public function getResult(string $query, array $parameters = null, string|array $types = null): ResultInterface;

    public function insert(string $query, array $parameters, string|array $types = null): int;

    public function prepare(string $query, array $parameters = null, string|array $types = null): StatementInterface;

    public function update(string $query, array $parameters, string|array $types = null): int;
}