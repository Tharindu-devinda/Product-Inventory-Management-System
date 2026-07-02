<div class="py-0 mx-2">
    <div class="max-w-4xl mx-auto mt-8 py-6 px-4 bg-white rounded-lg shadow-md">
        <section class="text-center mb-6">
            <h1 class="text-2xl font-bold">Edit Order #<?= $order['id'] ?></h1>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Customer :</label>
                <p class="mt-2 text-gray-900 font-medium"><?php
                                                            $customer = array_filter($customers, fn($c) => $c['id'] == $order['customer_id']);
                                                            $customer = reset($customer);
                                                            echo $customer['name'] . ' (' . $customer['email'] . ')';
                                                            ?></p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Order Date :</label>
                <p class="mt-2 text-gray-900 font-medium"><?= date('Y-m-d', strtotime($order['order_date'])) ?></p>
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
                        <th class="border border-gray-300 px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <?php
                    $currentTotal = 0;
                    foreach ($orderDetails as $detail):
                        $itemTotal = $detail['quantity'] * $detail['price'];
                        $currentTotal += $itemTotal;
                    ?>
                        <tr class="hover:bg-orange-50">
                            <td class="border border-gray-300 px-4 py-2"><?= $detail['product_name'] ?> (<?= $detail['sku_code'] ?>)</td>
                            <td class="border border-gray-300 px-4 py-2 text-right"><?= $detail['quantity'] ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-right">Rs. <?= number_format($detail['price'], 2) ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-right font-bold">Rs. <?= number_format($itemTotal, 2) ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <button type="button" class="delete-item-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm" data-id="<?= $detail['id'] ?>">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-amber-100 font-bold">
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-right">Current Total:</td>
                        <td id="currentTotalCell" class="border border-gray-300 px-4 py-2 text-right">Rs. <?= number_format($currentTotal, 2) ?></td>
                        <td class="border border-gray-300 px-4 py-2"></td>
                    </tr>
                </tfoot>
            </table>

            <p id="noItemsMsg" class="text-center text-gray-600 mt-4 <?= count($orderDetails) > 0 ? 'hidden' : '' ?>">No items remaining in this order</p>
        </div>

        <div class="flex gap-2 mt-6">
            <a href="/orders"
                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-center">
                Back to Orders
            </a>
        </div>
    </div>
</div>

<!-- CONFIRMATION MODAL -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-black/25 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Confirm Delete</h2>
        <p class="text-gray-700 mb-6">Are you sure you want to delete this item from the order?</p>
        <div class="flex gap-2">
            <button id="confirmDeleteBtn" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                Delete
            </button>
            <button id="cancelDeleteBtn" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let orderId = '<?= $order["id"] ?>';
        let itemToDelete = null;

        // TOAST NOTIFICATION HELPER
        function showToast(message, type = 'success') {
            let bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            let toast = $(`
                <div class="fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg font-bold text-base z-50">
                    ${message}
                </div>
            `);

            $('body').append(toast);

            setTimeout(() => {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }

        // DELETE ITEM BUTTON CLICK
        $(document).on('click', '.delete-item-btn', function() {
            itemToDelete = $(this).data('id');
            $('#deleteConfirmModal').removeClass('hidden').addClass('flex');
        });

        // CONFIRM DELETE
        $('#confirmDeleteBtn').click(function() {
            if (!itemToDelete) return;

            $.ajax({
                url: `/orders/items/${itemToDelete}/delete`,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Find the row with this item ID and remove it
                        let row = $(`button[data-id="${itemToDelete}"]`).closest('tr');
                        row.fadeOut(300, function() {
                            $(this).remove();

                            // Use the authoritative total returned by the server
                            // (recalculated from the database, not from the DOM)
                            $('#currentTotalCell').text('Rs. ' + response.new_total);

                            // Show "no items" message if the table is now empty
                            if ($('#itemsTableBody tr').length === 0) {
                                $('#noItemsMsg').removeClass('hidden');
                            }

                            showToast('Item deleted successfully!', 'success');
                        });
                    } else {
                        showToast(response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Failed to delete item', 'error');
                },
                complete: function() {
                    closeDeleteModal();
                }
            });
        });

        // CLOSE MODAL
        function closeDeleteModal() {
            $('#deleteConfirmModal').addClass('hidden').removeClass('flex');
            itemToDelete = null;
        }

        $('#cancelDeleteBtn').click(closeDeleteModal);

        // CLOSE MODAL WHEN CLICKING OUTSIDE
        $(document).click(function(e) {
            if ($(e.target).is('#deleteConfirmModal')) {
                closeDeleteModal();
            }
        });
    });
</script>