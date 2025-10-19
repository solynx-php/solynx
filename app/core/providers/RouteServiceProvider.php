<?php

namespace app\core\providers;
use app\core\Application;


class RouteServiceProvider {
    public static function registerRoutes(Application $app): void {
        require_once dirname(__DIR__, 3) . '/routes/web.php';
        require_once dirname(__DIR__, 3) . '/routes/api.php';
    }
}