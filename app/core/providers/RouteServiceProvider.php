<?php

namespace app\core\providers;

use app\core\Application;
use app\core\Router;

class RouteServiceProvider {
    public static function registerRoutes(Application $app): void {
        $router = $app->router;

        (function (Router $router) {
            require dirname(__DIR__, 3) . '/routes/web.php';
        })($router);

        $router->group('api', function (Router $router) {
            require dirname(__DIR__, 3) . '/routes/api.php';
        });
    }
}
