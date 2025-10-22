<?php
use app\core\Application;

require __DIR__ . '/../vendor/autoload.php';

$ROOT_DIR = dirname(__DIR__) . '/app';

$app = new Application($ROOT_DIR);
return $app;
