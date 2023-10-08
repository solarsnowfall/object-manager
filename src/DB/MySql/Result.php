<?php

namespace SSF\ORM\DB\MySql;

use mysqli_result;
use SSF\ORM\DB\ResultInterface;

class Result implements ResultInterface
{
    public function __construct(
        private readonly mysqli_result $result
    ){}

    public function getResult(): mysqli_result
    {
        return $this->result;
    }

    public function close(): void
    {
        $this->result->close();
    }

    public function fetchAll(): array
    {
        $rows = [];
        while ($row = $this->result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function fetchOne(): array
    {
        return $this->result->fetch_assoc() ?? [];
    }

    public function numRows(): int
    {
        return $this->result->num_rows;
    }
}