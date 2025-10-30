<?php

use app\core\Config;

if (!function_exists('config')) {
    function config(string|array $key = null, $default = null)
    {
        $cfg = Config::instance();

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $cfg->set($k, $v);
            }
            return true;
        }

        if ($key === null) {
            return $cfg->all();
        }

        return $cfg->get($key, $default);
    }
}

function asset(string $path) {
    $base = $_ENV['APP_URL'] ?? 'http://localhost';
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}
