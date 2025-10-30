<?php

use app\controllers\Post\PostController;

$router->get('/posts', [PostController::class, 'index']);

$router->group(['prefix' => 'v1'], function ($router) {
    $router->get('/posts', [PostController::class, 'index']);
});
