<?php

/**
 * Create Order View
 * 
 * @var array $customers List of all customers
 * @var array $products List of all products
 */
?>

<div class="py-0 mx-2">
    <div class="max-w-4xl mx-auto mt-8 py-6 px-4 bg-white rounded-lg shadow-md">
        <!-- SELECT2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <!-- SELECT2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <section class="text-center mb-6">
            <h1 class="text-2xl font-bold">Create Order</h1>
        </section>

        <form id="orderForm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group md:col-span-2">
                    <label for="customer_id">Customer :</label>
                    <div class="flex gap-2">
                        <select id="customer_id" name="customer_id"
                            class="flex-2 mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                            required>
                            <option value="">-- Select Customer --</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>">
                                    <?= $customer['name'] ?> (<?= $customer['email'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" id="addCustomerBtn" class="mt-1 flex-2 bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded text-sm">
                            + New
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="order_date">Order Date :</label>
                    <input type="date" id="order_date" name="order_date" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        required>
                </div>
            </div>

            <!-- PRODUCTS SECTION -->
            <div class="mt-6 border-t pt-6">
                <h2 class="text-lg font-bold mb-4">Order Items</h2>

                <div class="mb-4">
                    <label for="product_select">Add Product :</label>
                    <select id="product_select"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Select Product --</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>" data-name="<?= $product['name'] ?>" data-sku="<?= $product['sku_code'] ?>">
                                <?= $product['name'] ?> - Rs. <?= number_format($product['price'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <div>
                        <label for="quantity">Quantity :</label>
                        <input type="number" id="quantity" min="1" value="1"
                            class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="unit_price">Unit Price :</label>
                        <input type="text" id="unit_price" disabled
                            class="w-full mt-1 border border-gray-300 p-2 rounded-lg bg-gray-100">
                    </div>
                    <div class="flex items-end">
                        <button type="button" id="addItemBtn"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                            + Add Item
                        </button>
                    </div>
                </div>

                <!-- ITEMS TABLE -->
                <div id="itemsContainer" class="hidden">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-amber-500 text-white">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">Product</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Qty</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Unit Price</th>
                                <th class="border border-gray-300 px-4 py-2 text-right">Total</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Items will be added here -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-amber-100 font-bold">
                                <td colspan="3" class="border border-gray-300 px-4 py-2 text-right">Order Total:</td>
                                <td class="border border-gray-300 px-4 py-2 text-right">Rs. <span id="totalAmount">0.00</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p id="noItemsMsg" class="text-center text-gray-600 mt-4">No items added yet</p>
            </div>

            <button type="submit"
                class="btn bg-amber-500 hover:bg-amber-600 mt-6 text-white font-bold py-2 px-4 rounded w-full">
                Create Order
            </button>
        </form>

        <a href="/orders"
            class="block mt-2 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded w-full text-center">
            Back to Orders
        </a>
    </div>
</div>

<!-- CREATE CUSTOMER MODAL -->
<div id="customerModal" class="fixed inset-0 bg-black/25 hidden items-center justify-center pointer-events-none z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md relative pointer-events-auto">
        <button type="button" id="closeCustomerModal" class="absolute top-1 right-2 text-3xl">&times;</button>

        <h2 class="text-2xl font-bold mb-6">Create New Customer</h2>

        <form id="customerForm">
            <div class="form-group mb-4">
                <label for="customer_name">Customer Name :</label>
                <input type="text" id="customer_name" name="name" minlength="2" maxlength="100"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Enter customer name" required>
                <small class="text-red-500 customer-name-error hidden"></small>
            </div>

            <div class="form-group mb-4">
                <label for="customer_email">Email :</label>
                <input type="email" id="customer_email" name="email" maxlength="100"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Enter customer email" required>
                <small class="text-red-500 customer-email-error hidden"></small>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded">
                    Create Customer
                </button>
                <button type="button" id="cancelCustomerBtn" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        let orderItems = [];

        // Initialize Select2
        $('#product_select').select2({
            placeholder: '-- Search Product --',
            allowClear: true,
            width: '100%'
        });

        // Update price when product changes
        $('#product_select').change(function() {
            $('#unit_price').val(parseFloat($(this).find('option:selected').data('price') || 0).toFixed(2));
        });

        // Add item to order
        $('#addItemBtn').click(function() {
            const productSelect = $('#product_select');
            const productId = productSelect.val();
            const quantity = parseInt($('#quantity').val()) || 1;
            const price = parseFloat($('#unit_price').val()) || 0;

            if (!productId) {
                showToast('Please select a product', 'error');
                return;
            }
            if (quantity <= 0) {
                showToast('Quantity must be greater than 0', 'error');
                return;
            }
            if (orderItems.find(item => item.product_id == productId)) {
                showToast('This product is already added', 'error');
                return;
            }

            orderItems.push({
                product_id: productId,
                product_name: productSelect.find('option:selected').data('name'),
                sku_code: productSelect.find('option:selected').data('sku'),
                quantity: quantity,
                price: price
            });

            renderItems();
            productSelect.val('').trigger('change');
            $('#quantity').val(1);
            $('#unit_price').val('');
        });

        // Remove item from order
        $(document).on('click', '.remove-item-btn', function() {
            orderItems = orderItems.filter(item => item.product_id != $(this).data('id'));
            renderItems();
        });

        // Render items table
        function renderItems() {
            const tbody = $('#itemsTableBody').empty();
            let total = 0;

            if (orderItems.length === 0) {
                $('#itemsContainer').addClass('hidden');
                $('#noItemsMsg').removeClass('hidden');
                return;
            }

            $('#itemsContainer').removeClass('hidden');
            $('#noItemsMsg').addClass('hidden');

            orderItems.forEach(item => {
                const itemTotal = item.quantity * item.price;
                total += itemTotal;
                tbody.append(`
                    <tr class="hover:bg-orange-50">
                        <td class="border border-gray-300 px-4 py-2">${item.product_name} (${item.sku_code})</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${item.quantity}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">Rs. ${item.price.toFixed(2)}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right font-bold">Rs. ${itemTotal.toFixed(2)}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <button type="button" class="remove-item-btn bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm" data-id="${item.product_id}">Remove</button>
                        </td>
                    </tr>
                `);
            });

            $('#totalAmount').text(total.toFixed(2));
        }

        // Submit order
        $('#orderForm').submit(function(e) {
            e.preventDefault();
            sendAjax({
                url: '/orders/store',
                method: 'POST',
                dataType: 'json',
                data: {
                    customer_id: $('#customer_id').val(),
                    order_date: $('#order_date').val(),
                    order_items: JSON.stringify(orderItems)
                },
                errorMsg: 'Failed to create order',
                success: (response) => {
                    if (response.success) {
                        showToast('Order created successfully!');
                        setTimeout(() => window.location.href = '/orders', 1500);
                    } else {
                        showToast(response.message + (response.errors ? ' ' + Object.values(response.errors).join(', ') : ''), 'error');
                    }
                }
            });
        });

        // Customer modal listeners
        setupModalListeners('customerModal', 'addCustomerBtn', 'closeCustomerModal', 'cancelCustomerBtn');

        // Submit customer form
        $('#customerForm').submit(function(e) {
            e.preventDefault();
            const name = $('#customer_name').val().trim();
            const email = $('#customer_email').val().trim();

            sendAjax({
                url: '/orders/customer/create',
                method: 'POST',
                dataType: 'json',
                data: {
                    name,
                    email
                },
                errorMsg: 'Failed to create customer',
                success: (response) => {
                    if (response.success) {
                        $('#customer_id').append(`<option value="${response.customer_id}" selected>${response.customer_name} (${response.customer_email})</option>`);
                        toggleModal('customerModal', false);
                        showToast('Customer created successfully!');
                    } else if (response.errors) {
                        if (response.errors.name) $('.customer-name-error').removeClass('hidden').text(response.errors.name);
                        if (response.errors.email) $('.customer-email-error').removeClass('hidden').text(response.errors.email);
                    } else {
                        showToast(response.message, 'error');
                    }
                }
            });
        });
    });
</script>