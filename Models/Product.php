<?php
declare(strict_types=1);

namespace Models;

use Config\DbConnector;

class Product
{
    private \PDO $conn;

    public function __construct()
    {
        $db = new DbConnector();
        $this->conn = $db->connect();
    }

    /**
     * Create a new product
     * @return int|false Returns the new product ID on success, or false on failure
     */
    public function createProduct(string $name, string $skuCode, float $price, string $description, int $supplierId, int $userId): int|false
    {
        $sql = "INSERT INTO product (name, sku_code, price, description, supplier_id, user_id)
            VALUES (:name, :sku_code, :price, :description, :supplier_id, :user_id)";

        $stmt = $this->conn->prepare($sql);

        $success = $stmt->execute([
            ':name' => $name,
            ':sku_code' => $skuCode,
            ':price' => $price,
            ':description' => $description,
            ':supplier_id' => $supplierId,
            ':user_id' => $userId
        ]);

        if ($success) {
            return (int) $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Get all products (excluding deleted ones)
     * @return array Returns an array of products
     */
    public function getAllProducts(): array
    {
        $sql = "SELECT p.id, p.name, p.sku_code, p.price, p.description, p.supplier_id, 
                COALESCE(i.quantity, 0) as quantity, COALESCE(i.status, 'active') as status
                FROM product p
                LEFT JOIN inventory i ON p.id = i.product_id
                WHERE p.deleted_at IS NULL";
        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get single product by ID
     * @return array|null Returns the product data or null if not found
     */
    public function getProductById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM product WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    /**
     * Check if SKU code already exists
     * @return bool Returns true if the SKU code exists, false otherwise
     */
    public function skuExists(string $skuCode): bool
    {
        $sql = "SELECT id FROM product WHERE sku_code = :sku_code AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':sku_code' => $skuCode]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Get all suppliers 
     * @return array Returns an array of suppliers
     */
    public function getAllSuppliers(): array
    {
        $sql = "SELECT id, name FROM supplier WHERE deleted_at IS NULL ORDER BY name";
        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Update a product
     * @return bool Returns true on success, false on failure
     */
    public function updateProduct(int $id, string $name, string $skuCode, float $price, string $description, int $supplierId): bool
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

    /**
     * Soft delete product by ID
     * @return bool Returns true on success, false on failure
     */
    public function softDelete(int $id): bool
    {
        $sql = "UPDATE product SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Get product images by product ID
     * @return array Returns an array of image filenames
     */
    public function getProductImages(int $productId): array
    {
        $uploadDir = dirname(__DIR__) . '/images/product-images/' . $productId;

        if (!is_dir($uploadDir)) {
            return [];
        }

        $files = array_diff(scandir($uploadDir), ['.', '..']);

        return array_values($files);
    }

    /**
     * Create inventory record for a product
     * @return bool Returns true on success, false on failure
     */
    public function createInventory(int $productId, int $quantity): bool
    {
        $sql = "INSERT INTO inventory (product_id, quantity, warehouse_id, status) 
                VALUES (:product_id, :quantity, 1, 'active')";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':product_id' => $productId,
            ':quantity' => $quantity
        ]);
    }

    /**
     * Get inventory for a product
     * @return array|null Returns inventory data or null if not found
     */
    public function getInventory(int $productId): ?array
    {
        $sql = "SELECT id, product_id, quantity, warehouse_id, status FROM inventory 
                WHERE product_id = :product_id LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    /**
     * Update inventory quantity for a product
     * @return bool Returns true on success, false on failure
     */
    public function updateInventory(int $productId, int $quantity): bool
    {
        $sql = "UPDATE inventory SET quantity = :quantity WHERE product_id = :product_id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':quantity' => $quantity,
            ':product_id' => $productId
        ]);
    }
}