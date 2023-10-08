<?php

namespace SSF\ORM\DB\MySql;

use SSF\ORM\DB\AdapterInterface;
use SSF\ORM\DB\ResultInterface;
use SSF\ORM\DB\StatementInterface;

class MySql implements AdapterInterface
{
    public function __construct(
        private readonly Connection $connection
    ){}

    public function affectedRows(): int
    {
        return $this->connection->affectedRows();
    }

    public function delete(string $query, array $parameters, array|string $types = null): int
    {
        return $this->execute($query, $parameters, $types)->affectedRows();
    }

    public function errorCode(): int
    {
        return $this->connection->errorCode();
    }

    public function errorMessage(): string
    {
        return $this->connection->errorMessage();
    }

    public function execute(string $query, array $parameters = null, array|string $types = null): StatementInterface
    {
        return $this->prepare($query, $parameters, $types)->execute();
    }

    public function fetchOne(string $query, array $parameters = null, array|string $types = null): array
    {
        return $this->execute($query, $parameters, $types)->fetchOne();
    }

    public function fetchAll(string $query, array $parameters = null, array|string $types = null): array
    {
        return $this->execute($query, $parameters, $types)->fetchAll();
    }

    public function insert(string $query, array $parameters, array|string $types = null): int
    {
        return $this->execute($query, $parameters, $types)->lastInsertId();
    }

    public function lastInsertId(): int
    {
        return $this->connection->lastInsertId();
    }

    public function getResult(string $query, array $parameters = null, array|string $types = null): ResultInterface
    {
        return $this->execute($query, $parameters, $types)->getResult();
    }

    public function prepare(string $query, array $parameters = null, array|string $types = null): StatementInterface
    {
        return $this->connection->prepare($query, $parameters, $types);
    }

    public function update(string $query, array $parameters, array|string $types = null): int
    {
        return $this->execute($query, $parameters, $types)->affectedRows();
    }
}