<div class="py-0 mx-2">
    <div class="max-w-2xl mx-auto mt-8 py-6 px-4 bg-white rounded-lg shadow-md">
        <section class="text-center mb-6">
            <h1 class="text-2xl font-bold">Create Product</h1>
        </section>

        <form id="productForm">
            <div class="form-group mt-1">
                <label for="name">Product Name :</label>
                <input type="text" id="name" name="name" minlength="3" maxlength="100"
                    value="<?= $old['name'] ?? '' ?>"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Enter product name" required>
                <small class="text-red-500"></small>
            </div>

            <div class="form-group mt-1">
                <label for="sku_code">SKU Code :</label>
                <input type="text" id="sku_code" name="sku_code" minlength="3" maxlength="150"
                    value="<?= $old['sku_code'] ?? '' ?>"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="e.g., PROD-001" required>
                <small class="text-red-500"></small>
            </div>

            <div class="form-group mt-1">
                <label for="price">Price :</label>
                <input type="number" id="price" name="price" step="0.01" min="0"
                    value="<?= $old['price'] ?? '' ?>"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Enter product price" required>
                <small class="text-red-500"></small>
            </div>

            <div class="form-group mt-1">
                <label for="description">Description :</label>
                <textarea id="description" name="description" maxlength="500" rows="4"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Enter product description (optional)"><?= $old['description'] ?? '' ?></textarea>
                <small class="text-red-500"></small>
            </div>

            <div class="form-group mt-1">
                <label for="supplier_id">Supplier :</label>
                <select id="supplier_id" name="supplier_id"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    required>
                    <option value="">-- Select Supplier --</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= $supplier['id'] ?>" <?= ($old['supplier_id'] ?? '') == $supplier['id'] ? 'selected' : '' ?>>
                            <?= $supplier['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-red-500"></small>
            </div>

            <button type="submit" class="btn bg-amber-500 hover:bg-amber-600 mt-4 text-white font-bold py-2 px-4 rounded w-full">
                Create Product
            </button>
        </form>

        <a href="/products"
            class="block mt-2 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded w-full text-center">
            Back to Products
        </a>
    </div>
</div>

<script>
    document.getElementById("productForm").addEventListener("submit", function (e) {
        e.preventDefault();

        // Clear previous errors
        document.querySelectorAll('small.text-red-500').forEach(small => {
            small.textContent = '';
        });

        let formData = new FormData(this);

        fetch("/products/store", {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const successMsg = document.createElement('p');
                    successMsg.className = 'text-green-600 text-center font-bold';
                    successMsg.textContent = data.message;
                    document.querySelector('form').parentElement.insertBefore(successMsg, document.querySelector('form'));
                    document.getElementById("productForm").reset();

                    setTimeout(() => {
                        window.location.href = '/products';
                    }, 2000);
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = document.querySelector(`[name="${field}"]`);
                            if (input) {
                                const smallError = input.parentElement.querySelector('small');
                                if (smallError) {
                                    smallError.textContent = data.errors[field];
                                }
                            }
                        });
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>