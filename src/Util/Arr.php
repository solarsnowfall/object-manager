<?php

namespace SSF\ORM\Util;

class Arr
{
    public static function isSequential(array $array): bool
    {
        return [] === $array || range(0, count($array) - 1) === array_keys($array);
    }
}