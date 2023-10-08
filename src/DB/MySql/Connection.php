<?php

namespace SSF\ORM\DB\MySql;

use Exception;
use mysqli;
use mysqli_driver;
use SSF\ORM\DB\ConnectionInterface;
use SSF\ORM\DB\ResultInterface;
use SSF\ORM\DB\StatementInterface;

class Connection implements ConnectionInterface
{
    private mysqli $connection;

    private mysqli_driver $driver;

    /**
     * @param string|null $hostname
     * @param string|null $username
     * @param string|null $password
     * @param string|null $database
     * @param int|null $port
     * @param string|null $socket
     */
    public function __construct(
        private ?string $hostname = null,
        private ?string $username = null,
        private ?string $password = null,
        private ?string $database = null,
        private ?int $port = null,
        private ?string $socket = null
    ){
        $this->driver = new mysqli_driver();
        $this->driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
        $this->connect();
    }

    /**
     * @return void
     */
    public function connect(): void
    {
        $this->connection = new mysqli(
            $this->hostname,
            $this->username,
            $this->password,
            $this->database,
            $this->port,
            $this->socket
        );
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    public function affectedRows(): int
    {
        return $this->connection->affected_rows;
    }

    public function errorCode(): int
    {
        return $this->connection->errno;
    }

    public function errorMessage(): string
    {
        return $this->connection->error;
    }

    /**
     * @param string $query
     * @param array|null $parameters
     * @param string|array|null $types
     * @return StatementInterface
     * @throws Exception
     */
    public function execute(string $query, array $parameters = null, string|array $types = null): StatementInterface
    {
        try {

            $statement = $this->prepare($query, $parameters, $types);

            if (! $statement->execute()) {
                throw new Exception($statement->errorMessage(), $statement->errorCode());
            }

            return $statement;

        } catch (Exception $exception) {

            if (in_array($exception->getCode(), [2006, 2013])) {
                $this->connect();
                return $this->execute($query, $parameters, $types);
            } else {
                throw $exception;
            }
        }
    }

    public function getResult(string $query, array $parameters = null, array|string $types = null): ResultInterface
    {
        return $this->prepare($query, $parameters, $types)->execute()->getResult();
    }

    public function lastInsertId(): int
    {
        return $this->connection->insert_id;
    }

    public function prepare(string $query, array $parameters = null, string|array $types = null): StatementInterface
    {
        $statement = new Statement($this->connection->prepare($query));

        if (null !== $parameters) {
            $statement->bindParameters($parameters, $types);
        }

        return $statement;
    }


}