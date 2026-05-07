<?php
declare(strict_types=1);

namespace Models;

use Config\DbConnector;

class Supplier
{
    private \PDO $conn;

    public function __construct()
    {
        $db = new DbConnector();
        $this->conn = $db->connect();
    }

    public function getCount(): int
    {
        $stmt = $this->conn->query("SELECT COUNT(*) AS cnt FROM supplier WHERE deleted_at IS NULL");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return isset($row['cnt']) ? (int) $row['cnt'] : 0;
    }
}