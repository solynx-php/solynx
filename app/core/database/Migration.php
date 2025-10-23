<?php
namespace app\core\Database;

use PDO;

abstract class Migration {
    protected PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
        Schema::setConnection($db);
    }

    abstract public function up();
    abstract public function down();
}
