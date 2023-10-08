<?php

include 'bootstrap.php';

$cache = new \SSF\ORM\Cache\Simple\ArrayCache();
$cache->set('test', 'value', 1);
sleep(2);
echo $cache->get('test', 'gone');