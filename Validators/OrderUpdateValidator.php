<?php

declare(strict_types=1);

namespace Validators;

use Models\Order;

class OrderUpdateValidator
{
    private array $order;
    private ?Order $orderModel;

    public function __construct(array $order, ?Order $orderModel = null)
    {
        $this->order = $order;
        $this->orderModel = $orderModel;
    }

    /**
     * Validate order input for update
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
