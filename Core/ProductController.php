<?php

use Core\Controller;
use Models\Product;
use Traits\InputNormalizer;
use Validators\ProductValidator;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    use InputNormalizer;

    /**
     * Show product creation form
     *
     * @return string HTML view
     */
    public function index()
    {
        $productModel = new Product();
        $suppliers = $productModel->getAllSuppliers();

        return $this->view('create-product', ['suppliers' => $suppliers, 'errors' => [], 'old' => []]);
    }

    /**
     * Store a new product
     *
     * @param Request $request
     * @return string JSON response
     */
    public function store(Request $request)
    {
        try {
            $inputs = $this->normalizeInputs($request, ['name', 'sku_code', 'price', 'description', 'supplier_id']);

            $productModel = new Product();

            // Validation
            $validator = new ProductValidator($inputs, $productModel);
            $errors = $validator->validate();

            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
            }

            // Save to DB (user_id = 1 for now, ideally from session)
            $result = $productModel->createProduct(
                $inputs['name'],
                $inputs['sku_code'],
                $inputs['price'],
                $inputs['description'],
                $inputs['supplier_id'],
                1  // TODO: Get from authenticated user session
            );

            if ($result) {
                return $this->jsonResponse(true, 'Product created successfully');
            } else {
                return $this->jsonResponse(false, 'Failed to create product');
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Server error: ' . $e->getMessage());
        }
    }

    /**
     * Show all products
     *
     * @return string HTML view
     */
    public function list()
    {
        $productModel = new Product();
        $products = $productModel->getAllProducts();

        return $this->view('products', ['products' => $products]);
    }

    /**
     * Delete product (soft delete)
     *
     * @param Request $request
     * @return string JSON response
     */
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
}