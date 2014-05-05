<?php

require __DIR__ . '/../vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();
$loader->set('ErlCache\\', array(__DIR__ . '/../src'));
$loader->register(true);