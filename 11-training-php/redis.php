<?php
if (!class_exists('Redis')) {
    die('The Redis extension is not installed or enabled.');
}

$redis = new Redis();

    $redis->connect('web-redis', 6379, 2.5);

