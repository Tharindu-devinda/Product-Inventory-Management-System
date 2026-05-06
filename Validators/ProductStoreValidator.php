<?php
declare(strict_types=1);

namespace Validators;

use Models\Product;

class ProductStoreValidator
{
    private array $product;
    private ?Product $productModel;

    public function __construct(array $product, ?Product $productModel = null)
    {
        $this->product = $product;
        $this->productModel = $productModel;
    }

    /**
     * Validate product input
     * @return array Returns an array of validation errors
     */
    public function validate(): array
    {
        $errors = [];

        // Name validation
        if (empty($this->product['name'])) {
            $errors['name'] = 'Product name is required';
        } elseif (mb_strlen((string) ($this->product['name'] ?? '')) < 3) {
            $errors['name'] = 'Product name must be at least 3 characters';
        }

        // SKU code validation
        if (empty($this->product['sku_code'])) {
            $errors['sku_code'] = 'SKU code is required';
        } elseif (mb_strlen((string) ($this->product['sku_code'] ?? '')) < 3) {
            $errors['sku_code'] = 'SKU code must be at least 3 characters';
        } elseif ($this->productModel && $this->productModel->skuExists((string) $this->product['sku_code'])) {
            $errors['sku_code'] = 'SKU code already exists';
        }

        // Price validation
        if (!isset($this->product['price']) || $this->product['price'] === '') {
            $errors['price'] = 'Price is required';
        } elseif (!is_numeric($this->product['price']) || (float) $this->product['price'] <= 0) {
            $errors['price'] = 'Price must be a valid positive number';
        }

        // Description validation
        if (!empty($this->product['description']) && mb_strlen((string) $this->product['description']) > 500) {
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