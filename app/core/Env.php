<?php

namespace app\core;

class Env
{
    public static function load(string $path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException(".env file not found at: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);

            // skip comments
            if (str_starts_with($line, '#')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode('=', $line, 2));

            $value = trim($value, '"\''); // remove quotes
            $_ENV[$name] = $value;
            putenv("{$name}={$value}");
        }
    }

    public static function get(string $key, $default = null): mixed
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
