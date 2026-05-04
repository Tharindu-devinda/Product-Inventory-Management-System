<div class="py-5 mx-2">
    <div class="max-w-sm mx-auto mt-8 py-6 px-4 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center mb-4">Edit Product</h1>

        <form id="editProductForm">
            <div class="form-group mt-1">
                <label for="name">Product Name :</label>
                <input type="text" id="name" name="name" minlength="3" maxlength="120"
                    value="<?= htmlspecialchars($product['name']) ?>"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Enter product name" required>
                <small class="text-red-500"></small>
            </div>
            <div class="form-group mt-1">
                <label for="sku_code">SKU Code :</label>
                <input type="text" id="sku_code" name="sku_code" minlength="3" maxlength="50"
                    value="<?= htmlspecialchars($product['sku_code']) ?>"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="e.g. SKU-001" required>
                <small class="text-red-500"></small>
            </div>

            <div class="form-group mt-1">
                <label for="price">Price :</label>
                <input type="number" step="0.01" id="price" name="price"
                    value="<?= htmlspecialchars($product['price']) ?>"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="0.00" required>
                <small class="text-red-500"></small>
            </div>

            <div class="form-group mt-1">
                <label for="supplier_id">Supplier :</label>
                <select id="supplier_id" name="supplier_id"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    required>
                    <option value="">-- Select Supplier --</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $product['supplier_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-red-500"></small>
            </div>

            <div class="form-group mt-1">
                <label for="description">Description :</label>
                <textarea id="description" name="description"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    rows="4" placeholder="Optional"><?= htmlspecialchars($product['description']) ?></textarea>
                <small class="text-red-500"></small>
            </div>

            <button type="submit"
                class="btn bg-amber-500 hover:bg-amber-600 mt-4 text-white font-bold py-2 px-4 rounded w-full">
                Update Product
            </button>
        </form>

        <a href="/products"
            class="block mt-2 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded w-full text-center">
            Back to Products
        </a>
    </div>
</div>

<script>
    document.getElementById('editProductForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Clear previous errors
        document.querySelectorAll('small.text-red-500').forEach(s => s.textContent = '');

        const form = new FormData(this);
        const productId = <?= (int) $product['id'] ?>;

        fetch(`/products/${productId}/update`, { method: 'POST', body: form })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const successMsg = document.createElement('p');
                    successMsg.className = 'text-green-500 text-center font-bold';
                    successMsg.textContent = data.message || 'Product updated';
                    document.querySelector('form').parentElement.insertBefore(successMsg, document.querySelector('form'));

                    setTimeout(() => { window.location.href = '/products'; }, 1500);
                    return;
                }

                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            const small = input.parentElement.querySelector('small');
                            if (small) small.textContent = data.errors[field];
                        }
                    });
                } else {
                    alert(data.message || 'Update failed');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error');
            });
    });
</script>