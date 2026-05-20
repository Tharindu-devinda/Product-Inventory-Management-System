<div class="py-0 mx-2">
    <div class="max-w-4xl mx-auto mt-8 py-6 px-4 bg-white rounded-lg shadow-md">
        <section class="text-center mb-6">
            <h1 class="text-2xl font-bold">Edit Order #<?= $order['id'] ?></h1>
        </section>

        <form id="orderForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="customer_id">Customer :</label>
                    <select id="customer_id" name="customer_id"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        required>
                        <option value="">-- Select Customer --</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>" <?= $order['customer_id'] == $customer['id'] ? 'selected' : '' ?>>
                                <?= $customer['name'] ?> (<?= $customer['email'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-red-500"></small>
                </div>

                <div class="form-group">
                    <label for="order_date">Order Date :</label>
                    <input type="date" id="order_date" name="order_date" value="<?= date('Y-m-d', strtotime($order['order_date'])) ?>"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        required>
                    <small class="text-red-500"></small>
                </div>
            </div>

            <!-- CURRENT ITEMS SECTION -->
            <div class="mt-6 border-t pt-6">
                <h2 class="text-lg font-bold mb-4">Current Order Items</h2>

                <table class="w-full border-collapse border border-gray-300">
                    <thead class="bg-amber-500 text-white">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">Product</th>
                            <th class="border border-gray-300 px-4 py-2 text-right">Qty</th>
                            <th class="border border-gray-300 px-4 py-2 text-right">Unit Price</th>
                            <th class="border border-gray-300 px-4 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $currentTotal = 0;
                        foreach ($orderDetails as $detail):
                            $itemTotal = $detail['quantity'] * $detail['price'];
                            $currentTotal += $itemTotal;
                        ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><?= $detail['product_name'] ?> (<?= $detail['sku_code'] ?>)</td>
                                <td class="border border-gray-300 px-4 py-2 text-right"><?= $detail['quantity'] ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-right">Rs. <?= number_format($detail['price'], 2) ?></td>
                                <td class="border border-gray-300 px-4 py-2 text-right font-bold">Rs. <?= number_format($itemTotal, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="bg-amber-100 font-bold">
                            <td colspan="3" class="border border-gray-300 px-4 py-2 text-right">Current Total:</td>
                            <td class="border border-gray-300 px-4 py-2 text-right">Rs. <?= number_format($currentTotal, 2) ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 p-3 bg-blue-50 border border-blue-300 rounded text-sm text-blue-800">
                    <p><strong>Note:</strong> To modify order items, delete this order and create a new one with the updated items.</p>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit"
                    class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded">
                    Update Order
                </button>
                <a href="/orders"
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        let orderId = '<?= $order["id"] ?>';

        // SUBMIT FORM
        $('#orderForm').submit(function(e) {
            e.preventDefault();

            let customerId = $('#customer_id').val();
            let orderDate = $('#order_date').val();

            if (!customerId || !orderDate) {
                alert('Please fill in all required fields');
                return;
            }

            $.ajax({
                url: `/orders/${orderId}/update`,
                method: 'POST',
                dataType: 'json',
                data: {
                    customer_id: customerId,
                    order_date: orderDate,
                    order_items: JSON.stringify([])
                },
                success: function(response) {
                    if (response.success) {
                        alert('Order updated successfully!');
                        window.location.href = '/orders';
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Failed to update order');
                }
            });
        });
    });
</script>