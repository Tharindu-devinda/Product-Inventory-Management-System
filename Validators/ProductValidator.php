<?php

namespace Validators;

class ProductValidator
{
    private $product;
    private $productModel;

    public function __construct($product, $productModel = null)
    {
        $this->product = $product;
        $this->productModel = $productModel;
    }

    public function validate()
    {
        $errors = [];

        // Name validation
        if (empty($this->product['name'])) {
            $errors['name'] = 'Product name is required';
        } elseif (\strlen($this->product['name']) < 3) {
            $errors['name'] = 'Product name must be at least 3 characters';
        }

        // SKU code validation
        if (empty($this->product['sku_code'])) {
            $errors['sku_code'] = 'SKU code is required';
        } elseif (\strlen($this->product['sku_code']) < 3) {
            $errors['sku_code'] = 'SKU code must be at least 3 characters';
        } elseif ($this->productModel && $this->productModel->skuExists($this->product['sku_code'])) {
            $errors['sku_code'] = 'SKU code already exists';
        }

        // Price validation
        if (empty($this->product['price'])) {
            $errors['price'] = 'Price is required';
        } elseif (!is_numeric($this->product['price']) || $this->product['price'] <= 0) {
            $errors['price'] = 'Price must be a valid positive number';
        }

        // Description validation (optional, but if provided check length)
        if (!empty($this->product['description']) && \strlen($this->product['description']) > 500) {
            $errors['description'] = 'Description cannot exceed 500 characters';
        }

        // Supplier ID validation
        if (empty($this->product['supplier_id'])) {
            $errors['supplier_id'] = 'Supplier is required';
        } elseif (!is_numeric($this->product['supplier_id'])) {
            $errors['supplier_id'] = 'Invalid supplier selected';
        }

        return $errors;
    }
}