<?php

use app\controllers\Post\PostController;

$router->get('/posts', [PostController::class, 'index']);
