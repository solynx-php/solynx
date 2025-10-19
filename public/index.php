<?php
require_once __DIR__ . '/../vendor/autoload.php';

use app\core\Application;
use app\core\providers\RouteServiceProvider;

$ROOT_DIR = dirname(__DIR__) . '\app';
$app = new Application($ROOT_DIR);


RouteServiceProvider::registerRoutes($app);

$app->run();