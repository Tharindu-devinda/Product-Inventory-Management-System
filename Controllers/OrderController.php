<?php

use Core\Controller;
use Models\Order;
use Traits\InputNormalizer;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    use InputNormalizer;

    /**
     * Show order creation form
     */
    public function index(): string
    {
        $orderModel = new Order();
        $customers = $orderModel->getAllCustomers();
        $products = $orderModel->getAllProducts();

        return $this->view('create-order', [
            'customers' => $customers,
            'products' => $products,
            'errors' => [],
            'old' => []
        ]);
    }

    /**
     * Handle order creation form submission, validate input, save to DB
     * return JSON response
     */
    public function store(Request $request): string
    {
        try {
            $inputs = $this->normalizeInputs($request, ['customer_id', 'order_date', 'order_items']);

            $orderModel = new Order();

            // Validate required fields
            $errors = [];
            if (empty($inputs['customer_id'])) {
                $errors['customer_id'] = 'Customer is required';
            }
            if (empty($inputs['order_date'])) {
                $errors['order_date'] = 'Order date is required';
            }

            $orderItems = json_decode($_POST['order_items'] ?? '[]', true);
            if (empty($orderItems)) {
                $errors['order_items'] = 'At least one product is required';
            }

            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
            }

            // Calculate total amount
            $totalAmount = 0;
            foreach ($orderItems as $item) {
                $totalAmount += ($item['quantity'] * $item['price']);
            }

            // Create order
            $orderId = $orderModel->createOrder(
                (int)$inputs['customer_id'],
                $inputs['order_date'],
                $totalAmount,
                1  // todo: Get from authenticated user session
            );

            if (!$orderId) {
                return $this->jsonResponse(false, 'Failed to create order');
            }

            // Add order details
            foreach ($orderItems as $item) {
                $orderModel->addOrderDetail(
                    $orderId,
                    (int)$item['product_id'],
                    (int)$item['quantity'],
                    (float)$item['price']
                );
            }

            return $this->jsonResponse(true, 'Order created successfully', ['order_id' => $orderId]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show orders list page or return JSON data
     */
    public function list(): string
    {
        $orderModel = new Order();

        // If AJAX request, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $orders = $orderModel->getAllOrders();
            return $this->jsonResponse(true, 'Orders retrieved', ['orders' => $orders]);
        }

        // Otherwise render the view
        return $this->view('orders', ['orders' => []]);
    }

    /**
     * Show order details
     */
    public function show(Request $request): string
    {
        $orderId = (int) $request->attributes->get('id');

        $orderModel = new Order();
        $order = $orderModel->getOrder($orderId);

        if (!$order) {
            return $this->jsonResponse(false, 'Order not found');
        }

        $orderDetails = $orderModel->getOrderDetails($orderId);

        return $this->jsonResponse(true, 'Order details retrieved', [
            'order' => $order,
            'items' => $orderDetails
        ]);
    }

    /**
     * Show order edit form
     */
    public function edit(Request $request): string
    {
        $orderId = (int) $request->attributes->get('id');

        $orderModel = new Order();
        $order = $orderModel->getOrder($orderId);

        if (!$order) {
            return $this->jsonResponse(false, 'Order not found');
        }

        $customers = $orderModel->getAllCustomers();
        $products = $orderModel->getAllProducts();
        $orderDetails = $orderModel->getOrderDetails($orderId);

        return $this->view('edit-order', [
            'order' => $order,
            'customers' => $customers,
            'products' => $products,
            'orderDetails' => $orderDetails,
            'errors' => [],
            'old' => []
        ]);
    }

    /**
     * Update order
     */
    public function update(Request $request): string
    {
        try {
            $orderId = (int) $request->attributes->get('id');
            $inputs = $this->normalizeInputs($request, ['customer_id', 'order_date', 'order_items']);

            $orderModel = new Order();

            // Validate required fields
            $errors = [];
            if (empty($inputs['customer_id'])) {
                $errors['customer_id'] = 'Customer is required';
            }
            if (empty($inputs['order_date'])) {
                $errors['order_date'] = 'Order date is required';
            }

            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Validation errors', ['errors' => $errors]);
            }

            $orderItems = json_decode($_POST['order_items'] ?? '[]', true);

            // Calculate total amount
            $totalAmount = 0;
            foreach ($orderItems as $item) {
                $totalAmount += ($item['quantity'] * $item['price']);
            }

            // Update order
            $updated = $orderModel->updateOrder(
                $orderId,
                (int)$inputs['customer_id'],
                $inputs['order_date'],
                $totalAmount,
                1  // todo: Get from authenticated user session
            );

            if (!$updated) {
                return $this->jsonResponse(false, 'Failed to update order');
            }

            return $this->jsonResponse(true, 'Order updated successfully');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }
}
