<div class="py-5 mx-2">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">All Products</h1>
        <a href="/products/create" class="bg-amber-400 hover:bg-amber-500 text-white font-bold px-6 py-2 rounded">
            + Create Product
        </a>
    </div>

    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-amber-500 text-white">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">ID</th>
                    <th class="border border-gray-300 px-4 py-2">Name</th>
                    <th class="border border-gray-300 px-4 py-2">SKU</th>
                    <th class="border border-gray-300 px-4 py-2">Price</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr class="hover:bg-orange-100 cursor-pointer product-row" data-id="<?= $p['id'] ?>">
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($p['id']) ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($p['name'] ?? '') ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($p['sku_code'] ?? '') ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($p['price'] ?? '') ?></td>
                        <td class="border border-gray-300 px-4 py-2">
                            <a href="/products/<?= $p['id'] ?>/edit" class="bg-blue-500 text-white px-3 py-1 rounded">Edit</a>
                            <button data-id="<?= $p['id'] ?>"
                                class="delete-btn bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

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
        let selectedProductId = null;
        const deleteModal = document.getElementById('deleteModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                selectedProductId = this.dataset.id;
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            });
        });

        cancelBtn.addEventListener('click', function () {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        });

        confirmDeleteBtn.addEventListener('click', function () {
            fetch(`/products/${selectedProductId}/delete`, { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        deleteModal.classList.add('hidden');
                        deleteModal.classList.remove('flex');

                        const row = document.querySelector(`button[data-id="${selectedProductId}"]`).closest('tr');
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transition = 'opacity 0.3s ease';
                            setTimeout(() => row.remove(), 300);
                        }

                        const successMsg = document.createElement('div');
                        successMsg.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg font-bold text-base';
                        successMsg.style.zIndex = '9999';
                        successMsg.textContent = '✓ Product deleted successfully!';
                        document.body.appendChild(successMsg);

                        setTimeout(() => {
                            successMsg.style.opacity = '0';
                            successMsg.style.transition = 'opacity 0.3s ease';
                            setTimeout(() => successMsg.remove(), 300);
                        }, 3000);
                    } else {
                        alert(data.message || 'Delete failed');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Server error');
                });
        });

        // Open product modal on click
        document.querySelectorAll('.product-row').forEach(row => {
            row.addEventListener('click', function () {

                const productId = this.dataset.id;

                fetch(`/products/${productId}`)
                    .then(res => res.json())
                    .then(data => {

                        if (data.success) {

                            const product = data.product;

                            document.getElementById('modalContent').innerHTML = `
                        <p><strong>Name:</strong> ${product.name}</p>
                        <p><strong>SKU:</strong> ${product.sku_code}</p>
                        <p><strong>Price:</strong> ${product.price}</p>
                        <p><strong>Description:</strong> ${product.description ?? ''}</p>
                    `;

                            document.getElementById('productModal').classList.remove('hidden');
                            document.getElementById('productModal').classList.add('flex');

                        }
                    });
            });
        });

        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('productModal').classList.add('hidden');
        });

        // also close when clicking outside the content box
        document.getElementById('productModal').addEventListener('click', (e) => {
            if (e.target.id === 'productModal') {
                e.currentTarget.classList.add('hidden');
                e.currentTarget.classList.remove('flex');
            }
        });
    </script>
</div>