<?php
require_once __DIR__ . '/../vendor/autoload.php';
use app\core\Application;

$app = new Application();

// $router = new Router();

$app->router->get('/', function () {
    echo "Hello, World!";
});
$app->router->get('/contact', function () {
    echo "Contact!";
});

// $app->useRouter($router);
$app->run();