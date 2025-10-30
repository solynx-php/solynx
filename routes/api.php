<?php

use app\controllers\Post\PostController;

$router->get('/posts', [PostController::class, 'index']);

$router->group(['prefix' => 'v1'], function ($router) {

    $router->get('/posts', [PostController::class, 'index']);
});

$router->group(['prefix' => 'v1'], function ($router) {
    $router->group(['prefix' => 'v2', 'as' => 'posts'], function ($router) {

        $router->get('/posts/{id}', [PostController::class, 'show'])->name('index');
    });
});
