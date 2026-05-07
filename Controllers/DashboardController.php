<?php

use Core\Controller;
use Models\Product;
use Models\User;
use Models\Supplier;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    public function index(Request $request): string
    {
        $productModel = new Product();
        $userModel = new User();
        $supplierModel = new Supplier();

        $countProducts = count($productModel->getAllProducts());
        $countEmployees = $userModel->getCountByRole('employee');
        $countSuppliers = $supplierModel->getCount();

        $stats = [
            'products' => $countProducts,
            'employees' => $countEmployees,
            'suppliers' => $countSuppliers,
        ];

        return $this->view('dashboard', ['stats' => $stats]);
    }
}