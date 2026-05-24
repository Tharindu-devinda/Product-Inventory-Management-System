<?php

declare(strict_types=1);

namespace Models;

use Config\DbConnector;

class Order
{
    private \PDO $conn;

    public function __construct()
    {
        $db = new DbConnector();
        $this->conn = $db->connect();
    }

    /**
     * Create a new order
     * @return int|false Returns the new order ID on success, or false on failure
     */
    public function createOrder(int $customerId, string $orderDate, float $totalAmount, int $userId): int|false
    {
        $sql = "INSERT INTO orders (customer_id, order_date, total_amount, created_by)
                VALUES (:customer_id, :order_date, :total_amount, :created_by)";

        $stmt = $this->conn->prepare($sql);

        $success = $stmt->execute([
            ':customer_id' => $customerId,
            ':order_date' => $orderDate,
            ':total_amount' => $totalAmount,
            ':created_by' => $userId
        ]);

        if ($success) {
            return (int) $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Add order details
     */
    public function addOrderDetail(int $orderId, int $productId, int $quantity, float $price): bool
    {
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $productId,
            ':quantity' => $quantity,
            ':price' => $price
        ]);
    }

    /**
     * Get all orders with customer details (excluding deleted)
     * @return array Returns an array of orders
     */
    public function getAllOrders(): array
    {
        $sql = "SELECT o.id, o.customer_id, c.name as customer_name, c.email as customer_email,
                o.order_date, o.total_amount, COUNT(od.id) as total_items,
                o.created_at, o.updated_at
                FROM orders o
                LEFT JOIN customer c ON o.customer_id = c.id
                LEFT JOIN order_details od ON o.id = od.order_id
                WHERE o.deleted_at IS NULL
                AND od.deleted_at IS NULL
                GROUP BY o.id
                ORDER BY o.order_date DESC";

        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get order details with product information
     */
    public function getOrderDetails(int $orderId): array
    {
        $sql = "SELECT od.id, od.order_id, od.product_id, p.name as product_name, 
                p.sku_code, od.quantity, od.price, (od.quantity * od.price) as total
                FROM order_details od
                LEFT JOIN product p ON od.product_id = p.id
                WHERE od.order_id = :order_id AND od.deleted_at IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get single order with customer details
     */
    public function getOrder(int $orderId): array|false
    {
        $sql = "SELECT o.id, o.customer_id, c.name as customer_name, c.email as customer_email,
                o.order_date, o.total_amount, o.created_at, o.updated_at
                FROM orders o
                LEFT JOIN customer c ON o.customer_id = c.id
                WHERE o.id = :id AND o.deleted_at IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $orderId]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all customers
     */
    public function getAllCustomers(): array
    {
        $sql = "SELECT id, name, email FROM customer WHERE deleted_at IS NULL";
        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get all available products
     */
    public function getAllProducts(): array
    {
        $sql = "SELECT p.id, p.name, p.sku_code, p.price, 
                COALESCE(i.quantity, 0) as quantity
                FROM product p
                LEFT JOIN inventory i ON p.id = i.product_id
                WHERE p.deleted_at IS NULL";

        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Update order
     */
    public function updateOrder(int $orderId, int $customerId, string $orderDate, float $totalAmount, int $userId): bool
    {
        $sql = "UPDATE orders 
                SET customer_id = :customer_id, order_date = :order_date, 
                    total_amount = :total_amount, updated_by = :updated_by, 
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $orderId,
            ':customer_id' => $customerId,
            ':order_date' => $orderDate,
            ':total_amount' => $totalAmount,
            ':updated_by' => $userId
        ]);
    }

    /**
     * Soft delete an order
     */
    public function softDelete(int $orderId): bool
    {
        $sql = "UPDATE orders SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([':id' => $orderId]);
    }

    /**
     * Create a new customer
     * @return int|false Returns the new customer ID on success, or false on failure
     */
    public function createCustomer(string $name, string $email, int $userId): int|false
    {
        $sql = "INSERT INTO customer (name, email, created_by)
                VALUES (:name, :email, :created_by)";

        $stmt = $this->conn->prepare($sql);

        $success = $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':created_by' => $userId
        ]);

        if ($success) {
            return (int) $this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Soft delete an order detail (item)
     */
    public function deleteOrderDetail(int $orderDetailId): bool
    {
        $sql = "UPDATE order_details SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([':id' => $orderDetailId]);
    }
}
