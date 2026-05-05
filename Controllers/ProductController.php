<?php

use Core\Controller;
use Models\Product;
use Traits\InputNormalizer;
use Validators\ProductUpdateValidator;
use Validators\ProductValidator;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    use InputNormalizer;

    public function index()
    {
        $productModel = new Product();
        $suppliers = $productModel->getAllSuppliers();

        return $this->view('create-product', ['suppliers' => $suppliers, 'errors' => [], 'old' => []]);
    }

    public function store(Request $request)
    {
        try {
            $inputs = $this->normalizeInputs($request, ['name', 'sku_code', 'price', 'description', 'supplier_id']);

            $productModel = new Product();

            $validator = new ProductValidator($inputs, $productModel);
            $errors = $validator->validate();

            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
            }

            $images = $_FILES['images'] ?? null;

            $productId = $productModel->createProduct(
                $inputs['name'],
                $inputs['sku_code'],
                $inputs['price'],
                $inputs['description'],
                $inputs['supplier_id'],
                1  // todo: Get from authenticated user session
            );

            if (!$productId) {
                return $this->jsonResponse(false, 'Failed to create product');
            }

            // handle uploaded images
            $uploadDir = __DIR__ . '/../public/images/product-images/' . $productId;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($images && !empty($images['name'][0])) {

                // Limit max 5 images
                if (count($images['name']) > 5) {
                    return $this->jsonResponse(false, 'Max 5 images allowed');
                }

                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

                foreach ($images['name'] as $key => $name) {

                    if ($images['error'][$key] === 0) {

                        if (!in_array($images['type'][$key], $allowedTypes)) {
                            continue;
                        }

                        if ($images['size'][$key] > 2 * 1024 * 1024) {
                            continue;
                        }

                        $tmpName = $images['tmp_name'][$key];

                        $ext = pathinfo($name, PATHINFO_EXTENSION);
                        $fileName = uniqid('img_', true) . '.' . $ext;

                        $destination = $uploadDir . '/' . $fileName;

                        move_uploaded_file($tmpName, $destination);
                    }
                }
            }
            return $this->jsonResponse(true, 'Product created successfully');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }
    }

    public function list()
    {
        $productModel = new Product();
        $products = $productModel->getAllProducts();

        return $this->view('products', ['products' => $products]);
    }

    public function edit(Request $request)
    {
        $id = $request->attributes->get('id');
        $productModel = new Product();
        $product = $productModel->getProductById($id);

        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        $suppliers = $productModel->getAllSuppliers();

        return $this->view('edit-product', ['product' => $product, 'suppliers' => $suppliers, 'errors' => [], 'old' => []]);
    }

    public function update(Request $request)
    {
        try {
            $id = $request->attributes->get('id');
            $inputs = $this->normalizeInputs($request, ['name', 'sku_code', 'price', 'description', 'supplier_id']);

            $productModel = new Product();
            $product = $productModel->getProductById($id);

            if (!$product) {
                return $this->jsonResponse(false, 'Product not found');
            }

            // Run general validation (format/required/price/etc.)
            $validator = new ProductUpdateValidator($inputs, $productModel, $product);
            $errors = $validator->validate();

            // SKU uniqueness: only if SKU was changed
            if (empty($errors) || !isset($errors['sku_code'])) {
                if ($inputs['sku_code'] !== $product['sku_code'] && $productModel->skuExists($inputs['sku_code'])) {
                    $errors['sku_code'] = 'SKU code already exists';
                }
            }

            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
            }

            $updated = $productModel->updateProduct(
                $id,
                $inputs['name'],
                $inputs['sku_code'],
                $inputs['price'],
                $inputs['description'],
                $inputs['supplier_id']
            );

            if ($updated) {
                return $this->jsonResponse(true, 'Product updated successfully');
            }

            return $this->jsonResponse(false, 'Failed to update product');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->attributes->get('id');
            $productModel = new Product();
            $deleted = $productModel->softDelete($id);

            if ($deleted) {
                return $this->jsonResponse(true, 'Product deleted successfully');
            } else {
                return $this->jsonResponse(false, 'Product not found or already deleted');
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }
    }

    public function show(Request $request)
    {
        $id = $request->attributes->get('id');
        $productModel = new Product();
        $product = $productModel->getProductById($id);

        if (!$product) {
            return $this->jsonResponse(false, 'Product not found');
        }

        return $this->jsonResponse(true, '', ['product' => $product]);
    }
}