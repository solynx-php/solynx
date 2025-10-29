<?php
use app\controllers\SiteController;
use app\controllers\AuthController;
use app\controllers\Post\PostController;

$router->get('/', 'home');
$router->get('/contact', [SiteController::class, 'contact']);
$router->post('/contact', [SiteController::class, 'handleContactForm']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
