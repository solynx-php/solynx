<?php
namespace app\core\database;

use app\core\Database;
use PDO;

final class DB {
    public static function pdo(): PDO {
        return Database::connect();
    }
}
