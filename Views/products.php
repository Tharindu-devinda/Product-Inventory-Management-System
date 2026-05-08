<div class="py-5 mx-2">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">All Products</h1>
        <a href="/products/create" class="bg-amber-400 hover:bg-amber-500
         text-white font-bold px-6 py-2 rounded">
            + Create Product
        </a>
    </div>

    <div id="loadingSpinner" class="text-center py-4">
        <p class="text-gray-600">Loading products...</p>
    </div>

    <table id="productsTable" class="w-full border-collapse border border-gray-300 hidden">
        <thead class="bg-amber-500 text-white">
            <tr>
                <th class="border border-gray-300 px-4 py-2">ID</th>
                <th class="border border-gray-300 px-4 py-2">Name</th>
                <th class="border border-gray-300 px-4 py-2">SKU</th>
                <th class="border border-gray-300 px-4 py-2">Price</th>
                <th class="border border-gray-300 px-4 py-2">Quantity</th>
                <th class="border border-gray-300 px-4 py-2">Status</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            <!-- Products will be loaded here -->
        </tbody>
    </table>

    <div id="noProductsMsg" class="text-center py-6 hidden">
        <p class="text-gray-600 text-lg">No products found.</p>
    </div>

    <!-- DELETE MODAL -->
    <div id="deleteModal" class="fixed inset-0 hidden items-center justify-center bg-black/25">
        <div class="bg-white p-5 rounded-lg shadow-md w-80">
            <h2 class="text-lg font-bold mb-3">Confirm Delete</h2>
            <p class="mb-4">Are you sure you want to delete this product?</p>

            <div class="flex justify-end gap-2">
                <button id="cancelBtn" class="px-3 py-1 bg-gray-300 rounded">Cancel</button>
                <button id="confirmDeleteBtn" class="px-3 py-1 bg-red-500 text-white rounded">Delete</button>
            </div>
        </div>
    </div>

    <!-- PRODUCT DETAILS MODAL -->
    <div id="productModal" class="fixed inset-0 bg-black/25 hidden items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-1/2 relative">
            <button id="closeModal" class="absolute top-1 right-2 text-3xl">&times;</button>

            <h2 class="text-xl font-bold mb-4">Product Details</h2>

            <div id="modalContent">
                <!-- Data will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            let selectedProductId = null;
            let isDeleting = false;

            // LOAD PRODUCTS ON PAGE LOAD
            loadProducts();

            function loadProducts() {
                $.ajax({
                    url: '/products',
                    method: 'GET',
                    dataType: 'json',

                    success: function (data) {
                        $('#loadingSpinner').hide();

                        if (!data || (Array.isArray(data) && data.length === 0)) {
                            $('#noProductsMsg').removeClass('hidden');
                            return;
                        }

                        // Handle if data is an object with a products property
                        let products = Array.isArray(data) ? data : (data.products || []);

                        if (products.length === 0) {
                            $('#noProductsMsg').removeClass('hidden');
                            return;
                        }

                        let tbody = $('#productsTableBody');
                        tbody.empty();

                        $.each(products, function (index, p) {
                            let statusClass = p.status === 'in stock' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

                            let row = `
                                <tr class="hover:bg-orange-100" data-id="${p.id}">
                                    <td class="border border-gray-300 px-4 py-2 product-row cursor-pointer hover:bg-orange-200 hover:text-amber-600 transition-colors duration-150" data-id="${p.id}">
                                        ${p.id}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 product-row cursor-pointer hover:bg-orange-200 hover:text-amber-600 transition-colors duration-150" data-id="${p.id}">
                                        ${p.name}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">${p.sku_code}</td>
                                    <td class="border border-gray-300 px-4 py-2">${p.price}</td>
                                    <td class="border border-gray-300 px-4 py-2">${p.quantity}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <span class="${statusClass} px-3 py-1 rounded-full font-semibold text-sm">
                                            ${p.status}
                                        </span>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="/products/${p.id}/edit" class="bg-blue-500 text-white px-3 py-1 rounded">Edit</a>
                                        <button data-id="${p.id}" class="delete-btn bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                                    </td>
                                </tr>
                            `;

                            tbody.append(row);
                        });

                        $('#productsTable').removeClass('hidden');

                        // Re-bind event handlers for dynamically loaded rows
                        bindEventHandlers();
                    },

                    error: function (xhr, status, error) {
                        $('#loadingSpinner').html(`
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                <p class="font-bold">Failed to load products</p>
                                <p class="text-sm">Status: ${xhr.status} ${xhr.statusText}</p>
                                <p class="text-sm">Error: ${error}</p>
                                <p class="text-sm mt-2">Response: ${xhr.responseText.substring(0, 200)}</p>
                            </div>
                        `);
                    }
                });
            }

            function bindEventHandlers() {
                // DELETE BUTTON CLICK
                $('.delete-btn').off('click').on('click', function (e) {
                    e.stopPropagation();
                    selectedProductId = $(this).data('id');
                    $('#deleteModal').removeClass('hidden').addClass('flex');
                });

                // PRODUCT ROW CLICK → SHOW MODAL
                $('.product-row').off('click').on('click', function (e) {
                    if ($(e.target).closest('button, a').length) return;

                    let productId = $(this).data('id');

                    $.ajax({
                        url: `/products/${productId}`,
                        method: 'GET',
                        dataType: 'json',

                        success: function (data) {
                            if (data.success) {
                                let p = data.product;

                                let imagesHtml = p.images && p.images.length ? `
                                    <div class="mt-4">
                                        <p><strong>Images:</strong></p>
                                        <div class="grid grid-cols-3 gap-3">
                                            ${p.images.slice(0, 5).map(img => `
                                                <div class="bg-gray-100 rounded-lg overflow-hidden shadow">
                                                    <img src="${img}" alt="Product" class="w-full h-32 object-contain bg-white hover:scale-105 transition-transform">
                                                </div>
                                            `).join('')}
                                        </div>
                                        ${p.images.length > 5 ? `<p class="text-sm text-gray-500 mt-2">+${p.images.length - 5} more images</p>` : ''}
                                    </div>
                                ` : '<p class="text-gray-500 mt-4">No images</p>';

                                $('#modalContent').html(`
                                    <p><strong>Name:</strong> ${p.name}</p>
                                    <p><strong>SKU:</strong> ${p.sku_code}</p>
                                    <p><strong>Price:</strong> ${p.price}</p>
                                    <p><strong>Description:</strong> ${p.description ?? ''}</p>
                                    ${imagesHtml}
                                `);

                                $('#productModal').removeClass('hidden').addClass('flex');
                            } else {
                                alert(data.message || 'Failed to load product details');
                            }
                        },

                        error: function () {
                            alert('Error: Failed to load product details');
                        }
                    });
                });
            }

            // CANCEL DELETE
            $('#cancelBtn').on('click', function () {
                $('#deleteModal').addClass('hidden').removeClass('flex');
            });

            // CONFIRM DELETE
            $('#confirmDeleteBtn').on('click', function () {
                if (isDeleting) return;
                isDeleting = true;

                $.ajax({
                    url: `/products/${selectedProductId}/delete`,
                    method: 'POST',
                    dataType: 'json',

                    success: function (data) {
                        if (data.success) {
                            $('#deleteModal').addClass('hidden').removeClass('flex');
                            $('#productModal').addClass('hidden').removeClass('flex');

                            let row = $(`button[data-id="${selectedProductId}"]`).closest('tr');

                            row.fadeOut(300, function () {
                                $(this).remove();
                            });

                            // Success message
                            let successMsg = $(`
                                <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg font-bold text-base">
                                    Product deleted successfully!
                                </div>
                            `);

                            $('body').append(successMsg);

                            setTimeout(() => {
                                successMsg.fadeOut(300, function () {
                                    $(this).remove();
                                });
                            }, 3000);
                        } else {
                            alert(data.message || 'Delete failed');
                        }
                    },

                    error: function (xhr, status, error) {
                        alert('Error: Failed to delete product. Please try again.');
                    },

                    complete: function () {
                        isDeleting = false;
                    }
                });
            });

            // CLOSE PRODUCT MODAL
            $('#closeModal').on('click', function () {
                $('#productModal').addClass('hidden').removeClass('flex');
            });

            // CLICK OUTSIDE CLOSE
            $('#productModal').on('click', function (e) {
                if (e.target === this) {
                    $(this).addClass('hidden').removeClass('flex');
                }
            });

        });
    </script>
</div>