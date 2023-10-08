<?php

include 'vendor/autoload.php';

$container = \SSF\ORM\Dependency\Container::getContainer();

$container->set(\Memcached::class, function() {
    $memcached = new \Memcached;
    $memcached->addServer('localhost', 11211);
    return $memcached;
});