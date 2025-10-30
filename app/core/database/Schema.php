<?php
namespace app\core\database;

use PDO;

class Schema {
    public static PDO $db;

    public static function setConnection(PDO $db) {
        self::$db = $db;
    }

    public static function create(string $table, callable $callback) {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        self::$db->exec($blueprint->toSql());
    }

    public static function dropIfExists(string $table) {
        self::$db->exec("DROP TABLE IF EXISTS {$table}");
    }
}
