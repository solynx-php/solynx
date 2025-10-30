<?php
namespace app\core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection) {
            return self::$connection;
        }

        $db = config('db');

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $db['driver'] ?? 'mysql',
            $db['host'] ?? '127.0.0.1',
            $db['port'] ?? 3306,
            $db['database'] ?? ''
        );

        try {
            self::$connection = new PDO(
                $dsn,
                $db['username'] ?? '',
                $db['password'] ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }

        return self::$connection;
    }

    public static function query(string $sql, array $params = [])
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
