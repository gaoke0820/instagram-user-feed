<?php

use Instagram\Api;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

require realpath(dirname(__FILE__)) . '/../vendor/autoload.php';

$cachePool = new FilesystemAdapter('Instagram', 0, __DIR__ . '/../cache');

try {
    $api = new Api($cachePool);
    $api->login('user', 'password');
    $data = $api->getProfile('robertdowneyjr');
} catch (\Instagram\Exception\InstagramException $e) {
    print_r($e->getMessage());
}

dd($data);
