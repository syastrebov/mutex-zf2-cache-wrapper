<?php

require __DIR__ . '/../vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();
$loader->set('ErlCache\\', array('/'));
$loader->register();