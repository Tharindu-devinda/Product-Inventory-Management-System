<?php
declare(strict_types=1);

namespace Validators;

use Models\Product;

class ProductUpdateValidator
{
    private array $product;
    private ?Product $productModel;
    private ?array $currentProduct;

    public function __construct(array $product, ?Product $productModel = null, ?array $currentProduct = null)
    {
        $this->product = $product;
        $this->productModel = $productModel;
        $this->currentProduct = $currentProduct;
    }

    public function validate(): array
    {
        $errors = [];

        // Name
        if (empty($this->product['name'])) {
            $errors['name'] = 'Product name is required';
        } elseif (mb_strlen((string) ($this->product['name'] ?? '')) < 3) {
            $errors['name'] = 'Product name must be at least 3 characters';
        }

        // SKU
        if (empty($this->product['sku_code'])) {
            $errors['sku_code'] = 'SKU code is required';
        } elseif (mb_strlen((string) ($this->product['sku_code'] ?? '')) < 3) {
            $errors['sku_code'] = 'SKU code must be at least 3 characters';
        } else {
            // Only check uniqueness when SKU was changed (or when no current product provided)
            $skuChanged = !$this->currentProduct || ((string) ($this->product['sku_code'] ?? '') !== (string) ($this->currentProduct['sku_code'] ?? ''));
            if ($skuChanged && $this->productModel && $this->productModel->skuExists((string) $this->product['sku_code'])) {
                $errors['sku_code'] = 'SKU code already exists';
            }
        }

        // Price
        if (!isset($this->product['price']) || $this->product['price'] === '') {
            $errors['price'] = 'Price is required';
        } elseif (!is_numeric($this->product['price']) || (float) $this->product['price'] <= 0) {
            $errors['price'] = 'Price must be a valid positive number';
        }

        // Description (optional)
        if (!empty($this->product['description']) && mb_strlen((string) $this->product['description']) > 500) {
            $errors['description'] = 'Description cannot exceed 500 characters';
        }

        // Supplier
        if (empty($this->product['supplier_id'])) {
            $errors['supplier_id'] = 'Supplier is required';
        } elseif (!is_numeric($this->product['supplier_id'])) {
            $errors['supplier_id'] = 'Invalid supplier selected';
        }

        // Quantity validation
        if (!isset($this->product['quantity']) || $this->product['quantity'] === '') {
            $errors['quantity'] = 'Quantity is required';
        } elseif (!is_numeric($this->product['quantity']) || (int) $this->product['quantity'] < 0) {
            $errors['quantity'] = 'Quantity must be a valid non-negative number';
        }

        return $errors;
    }
}