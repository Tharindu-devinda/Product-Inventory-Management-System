<?php

use Core\Controller;
use Models\Product;
use Traits\InputNormalizer;
use Validators\ProductUpdateValidator;
use Validators\ProductStoreValidator;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    use InputNormalizer;

    public function index(): string
    {
        $productModel = new Product();
        $suppliers = $productModel->getAllSuppliers();

        return $this->view('create-product', ['suppliers' => $suppliers, 'errors' => [], 'old' => []]);
    }

    /**
     * Handle product creation form submission, validate input, save to DB
     * return JSON response
     */
    public function store(Request $request): string
    {
        try {
            $inputs = $this->normalizeInputs($request, ['name', 'sku_code', 'price', 'description', 'supplier_id']);

            $productModel = new Product();

            $validator = new ProductStoreValidator($inputs, $productModel);
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
            $uploadDir = dirname(__DIR__) . '/public/images/product-images/' . $productId;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($images && !empty($images['name'][0])) {

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

    /**
     * Display a list of all products
     * return HTML view with products data
     */
    public function list(): string
    {
        $productModel = new Product();
        $products = $productModel->getAllProducts();

        return $this->view('products', ['products' => $products]);
    }

    public function edit(Request $request): string
    {
        $id = $request->attributes->get('id');
        $productModel = new Product();
        $product = $productModel->getProductById($id);

        if ($product === null) {
            http_response_code(404);
            return "Product not found";
        }

        $suppliers = $productModel->getAllSuppliers();

        return $this->view('edit-product', ['product' => $product, 'suppliers' => $suppliers, 'errors' => [], 'old' => []]);
    }

    /**
     * Handle product edit form submission, validate input, update in DB
     * return JSON response with success status and message
     */
    public function update(Request $request): string
    {
        try {
            $id = $request->attributes->get('id');
            $inputs = $this->normalizeInputs($request, ['name', 'sku_code', 'price', 'description', 'supplier_id']);

            $productModel = new Product();
            $product = $productModel->getProductById($id);

            if ($product === null) {
                return $this->jsonResponse(false, 'Product not found');
            }

            $validator = new ProductUpdateValidator($inputs, $productModel, $product);
            $errors = $validator->validate();

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

    /**
     * Soft delete product by id
     * return JSON response indicating success or failure of deletion
     */
    public function delete(Request $request): string
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

    /**
     * Show single product details by id
     * return JSON response with product data or error message if not found
     */
    public function show(Request $request): string
    {
        $id = $request->attributes->get('id');
        $productModel = new Product();
        $product = $productModel->getProductById($id);

        if ($product === null) {
            return $this->jsonResponse(false, 'Product not found');
        }

        $imageFiles = $productModel->getProductImages($id);
        $images = array_map(fn($file) => '/public/images/product-images/' . $id . '/' . $file, $imageFiles);
        $product['images'] = $images;

        return $this->jsonResponse(true, '', ['product' => $product]);
    }
}