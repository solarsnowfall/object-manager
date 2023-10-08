<?php

namespace SSF\ORM\DB;

interface ResultInterface
{
    public function close(): void;

    public function fetchAll(): array;

    public function fetchOne(): array;

    public function numRows(): int;
}