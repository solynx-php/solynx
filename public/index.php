<?php
require_once __DIR__ . '/../vendor/autoload.php';
use app\core\Application;

$ROOT_DIR = dirname(__DIR__) . '\app';
$app = new Application($ROOT_DIR);

$app->router->get('/', 'home');
$app->router->get('/contact', 'contact');

$app->run();