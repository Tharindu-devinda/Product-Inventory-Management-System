<?php

namespace Models;

use Config\DbConnector;

class Product
{
    private $conn;

    public function __construct()
    {
        $db = new DbConnector();
        $this->conn = $db->connect();
    }

    // Create a new product
    public function createProduct($name, $skuCode, $price, $description, $supplierId, $userId)
    {
        $sql = "INSERT INTO product (name, sku_code, price, description, supplier_id, user_id)
                VALUES (:name, :sku_code, :price, :description, :supplier_id, :user_id)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':sku_code' => $skuCode,
            ':price' => $price,
            ':description' => $description,
            ':supplier_id' => $supplierId,
            ':user_id' => $userId
        ]);
    }

    // Get all products (excluding soft deleted)
    public function getAllProducts()
    {
        $sql = "SELECT id, name, sku_code, price, description, supplier_id FROM product WHERE deleted_at IS NULL";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Get single product by ID
    public function getProductById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM product WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Check if SKU code already exists
    public function skuExists($skuCode)
    {
        $sql = "SELECT id FROM product WHERE sku_code = :sku_code AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':sku_code' => $skuCode]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    // Get all suppliers
    public function getAllSuppliers()
    {
        $sql = "SELECT id, name FROM supplier WHERE deleted_at IS NULL ORDER BY name";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Update product
    public function updateProduct($id, $name, $skuCode, $price, $description, $supplierId)
    {
        $sql = "UPDATE product 
                SET name = :name, sku_code = :sku_code, price = :price, description = :description, supplier_id = :supplier_id 
                WHERE id = :id AND deleted_at IS NULL";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':sku_code' => $skuCode,
            ':price' => $price,
            ':description' => $description,
            ':supplier_id' => $supplierId,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

    // Soft delete product
    public function softDelete($id)
    {
        $sql = "UPDATE product SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}