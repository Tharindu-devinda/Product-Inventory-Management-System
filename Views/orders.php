<div class="py-5 mx-2">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">All Orders</h1>
        <a href="/orders/create" class="bg-amber-400 hover:bg-amber-500 text-white font-bold px-6 py-2 rounded">
            + Create Order
        </a>
    </div>

    <div id="loadingSpinner" class="text-center py-4">
        <p class="text-gray-600">Loading orders...</p>
    </div>

    <!-- ORDERS GRID -->
    <div id="ordersContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
        <!-- Order cards will be loaded here -->
    </div>

    <div id="noOrdersMsg" class="text-center py-6 hidden">
        <p class="text-gray-600 text-lg">No orders found.</p>
    </div>

    <!-- ORDER DETAILS MODAL -->
    <div id="orderModal" class="fixed inset-0 bg-black/25 hidden items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-full max-w-2xl relative max-h-[90vh] overflow-y-auto">
            <button id="closeModal" class="absolute top-1 right-2 text-3xl">&times;</button>

            <h2 class="text-2xl font-bold mb-4">Order Details</h2>

            <div id="modalContent">
                <!-- Data will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // LOAD ORDERS ON PAGE LOAD
            loadOrders();

            function loadOrders() {
                $.ajax({
                    url: '/orders',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#loadingSpinner').hide();

                        let orders = data.orders || [];

                        if (orders.length === 0) {
                            $('#noOrdersMsg').removeClass('hidden');
                            return;
                        }

                        let container = $('#ordersContainer');
                        container.empty();

                        $.each(orders, function(index, order) {
                            let orderDate = new Date(order.order_date);
                            let formattedDate = orderDate.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            });

                            let card = `
                                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 hover:shadow-lg transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800">Order #${order.id}</h3>
                                            <p class="text-sm text-gray-600">${formattedDate}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-amber-600">Rs. ${parseFloat(order.total_amount).toFixed(2)}</p>
                                            <p class="text-xs text-gray-500">${order.total_items} item(s)</p>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 rounded p-3 mb-4">
                                        <p class="text-sm text-gray-700"><strong>${order.customer_name}</strong></p>
                                        <p class="text-xs text-gray-600">${order.customer_email}</p>
                                    </div>

                                    <div class="flex gap-2">
                                        <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm view-btn" data-id="${order.id}">
                                            View
                                        </button>
                                        <a href="/orders/${order.id}/edit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 rounded text-sm text-center">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            `;
                            container.append(card);
                        });

                        container.removeClass('hidden');
                    },
                    error: function() {
                        $('#loadingSpinner').html('<p class="text-red-600">Failed to load orders</p>');
                    }
                });
            }

            // VIEW ORDER DETAILS
            $(document).on('click', '.view-btn', function() {
                let orderId = $(this).data('id');

                $.ajax({
                    url: `/orders/${orderId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let order = data.order;
                        let items = data.items || [];

                        let orderDate = new Date(order.order_date);
                        let formattedDate = orderDate.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });

                        let itemsHtml = '<table class="w-full border-collapse border border-gray-300 mt-4"><thead class="bg-amber-100"><tr><th class="border border-gray-300 px-3 py-2 text-left">Product</th><th class="border border-gray-300 px-3 py-2 text-right">Qty</th><th class="border border-gray-300 px-3 py-2 text-right">Price</th><th class="border border-gray-300 px-3 py-2 text-right">Total</th></tr></thead><tbody>';

                        $.each(items, function(i, item) {
                            itemsHtml += `
                                <tr>
                                    <td class="border border-gray-300 px-3 py-2">${item.product_name} (${item.sku_code})</td>
                                    <td class="border border-gray-300 px-3 py-2 text-right">${item.quantity}</td>
                                    <td class="border border-gray-300 px-3 py-2 text-right">Rs. ${parseFloat(item.price).toFixed(2)}</td>
                                    <td class="border border-gray-300 px-3 py-2 text-right font-bold">Rs. ${parseFloat(item.total).toFixed(2)}</td>
                                </tr>
                            `;
                        });

                        itemsHtml += '</tbody></table>';

                        let content = `
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Order Date</p>
                                        <p class="font-bold">${formattedDate}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Amount</p>
                                        <p class="font-bold text-lg text-amber-600">Rs. ${parseFloat(order.total_amount).toFixed(2)}</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded p-3">
                                    <p class="text-sm text-gray-600">Customer</p>
                                    <p class="font-bold">${order.customer_name}</p>
                                    <p class="text-sm text-gray-600">${order.customer_email}</p>
                                </div>
                                ${itemsHtml}
                            </div>
                        `;

                        $('#modalContent').html(content);
                        $('#orderModal').removeClass('hidden').addClass('flex');
                    },
                    error: function() {
                        alert('Failed to load order details');
                    }
                });
            });

            // CLOSE MODAL
            $('#closeModal').click(function() {
                $('#orderModal').addClass('hidden').removeClass('flex');
            });

            $(document).click(function(e) {
                if ($(e.target).is('#orderModal')) {
                    $('#orderModal').addClass('hidden').removeClass('flex');
                }
            });
        });
    </script>
</div>