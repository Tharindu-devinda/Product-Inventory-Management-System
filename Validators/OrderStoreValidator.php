<?php

declare(strict_types=1);

namespace Validators;

use Models\Order;

class OrderStoreValidator
{
    private array $order;
    private ?Order $orderModel;

    public function __construct(array $order, ?Order $orderModel = null)
    {
        $this->order = $order;
        $this->orderModel = $orderModel;
    }

    /**
     * Validate order input for creation
     * @return array Returns an array of validation errors
     */
    public function validate(): array
    {
        $errors = [];

        // Customer ID validation
        if (empty($this->order['customer_id'])) {
            $errors['customer_id'] = 'Customer is required';
        } elseif (!is_numeric($this->order['customer_id'])) {
            $errors['customer_id'] = 'Invalid customer selected';
        }

        // Order date validation
        if (empty($this->order['order_date'])) {
            $errors['order_date'] = 'Order date is required';
        } elseif (!$this->isValidDate($this->order['order_date'])) {
            $errors['order_date'] = 'Invalid order date format';
        }

        // Order items validation
        if (empty($this->order['order_items']) || count($this->order['order_items']) === 0) {
            $errors['order_items'] = 'At least one product is required';
        } else {
            foreach ($this->order['order_items'] as $index => $item) {
                if (empty($item['product_id']) || !is_numeric($item['product_id'])) {
                    $errors['order_items'] = 'Invalid product selected';
                    break;
                }
                if (empty($item['quantity']) || !is_numeric($item['quantity']) || (int)$item['quantity'] <= 0) {
                    $errors['order_items'] = 'Product quantity must be greater than 0';
                    break;
                }
                if (empty($item['price']) || !is_numeric($item['price']) || (float)$item['price'] < 0) {
                    $errors['order_items'] = 'Product price must be a valid number';
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Check if date is valid
     */
    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
