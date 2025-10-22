<?php
require_once __DIR__ . '/../vendor/autoload.php';

use app\core\Application;
use app\core\providers\RouteServiceProvider;

require_once __DIR__ . '/../bootstrap/app.php';


RouteServiceProvider::registerRoutes($app);

$app->run();