<?php
namespace app\core\Database;

use PDO;

class MigrationRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function all()
    {
        $stmt = $this->db->query("SELECT migration, batch, migrated_at FROM migrations ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function latestBatch()
    {
        $stmt = $this->db->query("SELECT MAX(batch) as batch FROM migrations");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['batch'] ?? 0);
    }

    public function log(string $file, int $batch)
    {
        $stmt = $this->db->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$file, $batch]);
    }

    public function isMigrated(string $file)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
        $stmt->execute([$file]);
        return $stmt->fetchColumn() > 0;
    }
}
