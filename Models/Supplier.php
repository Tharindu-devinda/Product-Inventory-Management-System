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

    /**
     * Get all suppliers (excluding deleted ones)
     * @return array Returns an array of suppliers
     */
    public function getCount(): int
    {
        $stmt = $this->conn->query("SELECT COUNT(*) AS cnt FROM supplier WHERE deleted_at IS NULL");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return isset($row['cnt']) ? (int) $row['cnt'] : 0;
    }
}