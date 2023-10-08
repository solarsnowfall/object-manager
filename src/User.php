<?php

namespace SSF\ORM;

use SSF\ORM\Object\Metadata\Column;
use SSF\ORM\Object\Metadata\DataType;
use SSF\ORM\Object\Metadata\PrimaryKey;
use SSF\ORM\Object\Metadata\Table;

#[Table(name: 'user')]
class User
{
    #[PrimaryKey]
    #[Column(dataType: DataType::MediumInt)]
    protected ?int $id = null;

    #[Column(dataType: DataType::Varchar)]
    protected ?string $email = null;

    #[Column(
        dataType: DataType::Varchar,
        maxLength: 20
    )]
    protected ?string $username = null;

    public function __toString(): string
    {
        return json_encode(get_object_vars($this));
    }
}