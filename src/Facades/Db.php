<?php

namespace SSF\ORM\Facades;

use SSF\ORM\DB\MySql\MySql;

class Db extends Facade
{

    protected static function instanceKey(): string
    {
        return MySql::class;
    }
}