<div class="py-0 mx-2">
    <div class="max-w-4xl mx-auto mt-8 py-6 px-4 bg-white rounded-lg shadow-md">
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
                    <small class="text-red-500"></small>
                </div>

                <div class="form-group">
                    <label for="order_date">Order Date :</label>
                    <input type="date" id="order_date" name="order_date" value="<?= date('Y-m-d') ?>"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        required>
                    <small class="text-red-500"></small>
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
<div id="customerModal" class="fixed inset-0 bg-black/25 hidden items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-full max-w-md relative">
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

        // UPDATE PRICE WHEN PRODUCT SELECTED
        $('#product_select').change(function() {
            let price = $(this).find('option:selected').data('price') || 0;
            $('#unit_price').val(parseFloat(price).toFixed(2));
        });

        // ADD ITEM TO ORDER
        $('#addItemBtn').click(function() {
            let productSelect = $('#product_select');
            let productId = productSelect.val();
            let productName = productSelect.find('option:selected').data('name');
            let productSku = productSelect.find('option:selected').data('sku');
            let quantity = parseInt($('#quantity').val()) || 1;
            let price = parseFloat($('#unit_price').val()) || 0;

            if (!productId) {
                alert('Please select a product');
                return;
            }

            if (quantity <= 0) {
                alert('Quantity must be greater than 0');
                return;
            }

            let existingItem = orderItems.find(item => item.product_id == productId);
            if (existingItem) {
                alert('This product is already added. Remove it first to add again.');
                return;
            }

            orderItems.push({
                product_id: productId,
                product_name: productName,
                sku_code: productSku,
                quantity: quantity,
                price: price
            });

            renderItems();
            productSelect.val('').trigger('change');
            $('#quantity').val(1);
            $('#unit_price').val('');
        });

        // REMOVE ITEM
        $(document).on('click', '.remove-item-btn', function() {
            let productId = $(this).data('id');
            orderItems = orderItems.filter(item => item.product_id != productId);
            renderItems();
        });

        function renderItems() {
            let tbody = $('#itemsTableBody');
            tbody.empty();
            let total = 0;

            if (orderItems.length === 0) {
                $('#itemsContainer').addClass('hidden');
                $('#noItemsMsg').removeClass('hidden');
                return;
            }

            $('#itemsContainer').removeClass('hidden');
            $('#noItemsMsg').addClass('hidden');

            $.each(orderItems, function(index, item) {
                let itemTotal = item.quantity * item.price;
                total += itemTotal;

                let row = `
                    <tr class="hover:bg-orange-50">
                        <td class="border border-gray-300 px-4 py-2">${item.product_name} (${item.sku_code})</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">${item.quantity}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">Rs. ${parseFloat(item.price).toFixed(2)}</td>
                        <td class="border border-gray-300 px-4 py-2 text-right font-bold">Rs. ${itemTotal.toFixed(2)}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <button type="button" class="remove-item-btn bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm" data-id="${item.product_id}">
                                Remove
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });

            $('#totalAmount').text(total.toFixed(2));
        }

        // SUBMIT ORDER FORM
        $('#orderForm').submit(function(e) {
            e.preventDefault();

            let customerId = $('#customer_id').val();
            let orderDate = $('#order_date').val();

            $.ajax({
                url: '/orders/store',
                method: 'POST',
                dataType: 'json',
                data: {
                    customer_id: customerId,
                    order_date: orderDate,
                    order_items: JSON.stringify(orderItems)
                },
                success: function(response) {
                    if (response.success) {
                        alert('Order created successfully!');
                        window.location.href = '/orders';
                    } else {
                        let errorMsg = response.message;
                        if (response.errors) {
                            errorMsg += '\n' + Object.values(response.errors).join('\n');
                        }
                        alert(errorMsg);
                    }
                },
                error: function() {
                    alert('Failed to create order');
                }
            });
        });

        // OPEN CREATE CUSTOMER MODAL
        $('#addCustomerBtn').click(function() {
            $('#customerModal').removeClass('hidden').addClass('flex');
        });

        // CLOSE CREATE CUSTOMER MODAL
        function closeCustomerModal() {
            $('#customerModal').addClass('hidden').removeClass('flex');
            $('#customerForm')[0].reset();
            $('.customer-name-error, .customer-email-error').addClass('hidden').text('');
        }

        $('#closeCustomerModal').click(closeCustomerModal);
        $('#cancelCustomerBtn').click(closeCustomerModal);

        // SUBMIT CREATE CUSTOMER FORM
        $('#customerForm').submit(function(e) {
            e.preventDefault();

            let name = $('#customer_name').val().trim();
            let email = $('#customer_email').val().trim();

            $.ajax({
                url: '/orders/customer/create',
                method: 'POST',
                dataType: 'json',
                data: {
                    name: name,
                    email: email
                },
                success: function(response) {
                    if (response.success) {
                        let option = $('<option></option>')
                            .val(response.customer_id)
                            .text(response.customer_name + ' (' + response.customer_email + ')')
                            .prop('selected', true);

                        $('#customer_id').append(option);
                        closeCustomerModal();
                        alert('Customer created successfully!');
                    } else {
                        if (response.errors) {
                            if (response.errors.name) {
                                $('.customer-name-error').removeClass('hidden').text(response.errors.name);
                            }
                            if (response.errors.email) {
                                $('.customer-email-error').removeClass('hidden').text(response.errors.email);
                            }
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function() {
                    alert('Failed to create customer');
                }
            });
        });

        // CLOSE MODAL WHEN CLICKING OUTSIDE
        $(document).click(function(e) {
            if ($(e.target).is('#customerModal')) {
                closeCustomerModal();
            }
        });
    });
</script>