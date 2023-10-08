<?php

namespace SSF\ORM\Facades;

use DateInterval;

/**
 * @method static int toSeconds(DateInterval|int|null $interval)
 */
class TTL extends Facade
{

    protected static function instanceKey(): string
    {
        return \SSF\ORM\Cache\Old\TTL::class;
    }
}