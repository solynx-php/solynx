<?php

use app\core\Config;

if (!function_exists('config')) {
    /**
     * Get or set config values.
     * config('db.host') -> get
     * config(['db.host' => '127.0.0.1']) -> set
     */
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

function asset(string $path): string {
    $base = $_ENV['APP_URL'] ?? 'http://localhost';
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}
