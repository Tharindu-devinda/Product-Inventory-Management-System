<?php
namespace Config;
class DbConnector
{
    private $host = 'localhost';
    private $db_name = 'inventory_management_system';
    private $username = 'root';
    private $password = '';

    public function connect()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name}";

        try {
            $conn = new PDO($dsn, $this->username, $this->password);
            return $conn;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}

?>