<?php
namespace app\core;

class Config
{
    private static ?self $instance = null;
    private array $data;

    private function __construct()
    {
        $env = Env::get('APP_ENV', 'local');
        $file = dirname(__DIR__, 2) . "/config/{$env}/config.php";
        if (!is_file($file)) {
            throw new \RuntimeException("Config file missing for environment: {$env}");
        }
        $this->data = require $file;
    }

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    public function get(string $key, $default = null): mixed
    {
        $cursor = $this->data;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($cursor) || !array_key_exists($segment, $cursor)) {
                return $default;
            }
            $cursor = $cursor[$segment];
        }
        return $cursor;
    }

    public function set(string $key, $value): void
    {
        $ref = &$this->data;
        $parts = explode('.', $key);
        foreach ($parts as $p) {
            if (!isset($ref[$p]) || !is_array($ref[$p])) {
                $ref[$p] = [];
            }
            $ref = &$ref[$p];
        }
        $ref = $value;
    }

    public function all(): array
    {
        return $this->data;
    }

    public static function reload(): void
    {
        self::$instance = null;
    }
}
