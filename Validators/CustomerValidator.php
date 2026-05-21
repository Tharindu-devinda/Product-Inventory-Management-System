<?php

declare(strict_types=1);

namespace Validators;

class CustomerValidator
{
    private array $customer;

    public function __construct(array $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Validate customer input
     * @return array Returns an array of validation errors
     */
    public function validate(): array
    {
        $errors = [];

        // Name validation
        if (empty($this->customer['name'])) {
            $errors['name'] = 'Customer name is required';
        } elseif (mb_strlen((string)($this->customer['name'] ?? '')) < 2) {
            $errors['name'] = 'Customer name must be at least 2 characters';
        } elseif (mb_strlen((string)($this->customer['name'] ?? '')) > 100) {
            $errors['name'] = 'Customer name cannot exceed 100 characters';
        }

        // Email validation
        if (empty($this->customer['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($this->customer['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } elseif (mb_strlen((string)($this->customer['email'] ?? '')) > 100) {
            $errors['email'] = 'Email cannot exceed 100 characters';
        }

        return $errors;
    }
}
