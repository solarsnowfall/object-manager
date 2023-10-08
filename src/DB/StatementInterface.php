<?php

namespace SSF\ORM\DB;

interface StatementInterface
{
    public function affectedRows(): int;

    public function bindParameters(
        int|float|string|null|array $parameters,
        string|array $types = null
    ): StatementInterface;

    public function bindResult(&...$vars): bool;

    public function bindResultArray(&$result): bool;

    public function close(): bool;

    public function errorCode(): int;

    public function errorMessage(): string;

    public function execute(array $parameters = null, array|string $types = null): StatementInterface;

    public function fetch(): bool;

    public function fetchAll(): array;

    public function fetchOne(): array;

    public function getResult(): ResultInterface;

    public function lastInsertId(): int;
}