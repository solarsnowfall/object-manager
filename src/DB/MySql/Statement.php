<?php

namespace SSF\ORM\DB\MySql;

use mysqli_stmt;
use SSF\ORM\DB\ResultInterface;
use SSF\ORM\DB\StatementInterface;

class Statement implements StatementInterface
{
    public function __construct(
        private readonly mysqli_stmt $statement
    ){}

    public function getStatement(): mysqli_stmt
    {
        return $this->statement;
    }

    public function affectedRows(): int
    {
        return $this->statement->affected_rows;
    }

    public function bindParameters(
        int|float|string|null|array $parameters,
        string|array $types = null
    ): StatementInterface {
        $this->statement->bind_param($this->resolveParameterTypes($parameters, $types), ...$parameters);
        return $this;
    }

    public function bindResult(&...$vars): bool
    {
        return $this->statement->bind_result(...$vars);
    }

    public function bindResultArray(&$result): bool
    {
        $parameters = [];
        $metadata = $this->statement->result_metadata();

        while ($field = $metadata->fetch_field()) {
            $parameters[] = &$result[$field->name];
        }

        return $this->bindResult(...$parameters);
    }

    public function close(): bool
    {
        return $this->statement->close();
    }

    public function errorCode(): int
    {
        return $this->statement->errno;
    }

    public function errorMessage(): string
    {
        return $this->statement->error;
    }

    public function execute(array $parameters = null, array|string $types = null): StatementInterface
    {
        if (null !== $parameters) {
            $this->bindParameters($parameters, $types);
        }

        if (! $this->statement->execute()) {
            throw new \RuntimeException(
                'Unable to execute statement',
                null,
                new \ErrorException($this->errorMessage(), $this->errorCode())
            );
        }

        return $this;
    }

    public function fetch(): bool
    {
        return $this->statement->fetch();
    }

    public function fetchOne(): array
    {
        return $this->getResult()->fetchOne();
        /*$this->bindResultArray($result);

        if (! $this->fetch()) {
            return [];
        }

        $row = [];
        foreach ($result as $key => $value) {
            $row[$key] = $value;
        }

        return $row;*/
    }

    public function fetchAll(): array
    {
        return $this->getResult()->fetchAll();
        /*$rows = [];
        while ($row = $this->fetchOne()) {
            $rows[] = $row;
        }

        return $rows;*/
    }

    public function getResult(): ResultInterface
    {
        return new Result($this->statement->get_result());
    }

    public function lastInsertId(): int
    {
        return $this->statement->insert_id;
    }

    private function resolveParameterTypes(array $parameters, string|array|null $types): string
    {
        if (is_string($types)) {
            return $types;
        }

        if (is_array($types)) {
            return implode($types);
        }

        return $this->guessParameterTypes($parameters);
    }

    private function guessParameterTypes($parameters): string
    {
        $types = '';
        foreach ((array) $parameters as $parameter) {
            if (ctype_digit((string) $parameter)) {
                $types .= $parameter < PHP_INT_MAX ? 'i' : 's';
            } elseif (is_numeric($parameter)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }

        return $types;
    }
}