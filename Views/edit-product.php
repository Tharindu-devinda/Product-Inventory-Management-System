<div class="py-5 mx-2">
    <div class="max-w-sm mx-auto mt-8 py-6 px-4 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center mb-4">Edit Product</h1>
        <form id="editProductForm">
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required />
            <input type="text" name="sku_code" value="<?= htmlspecialchars($product['sku_code']) ?>" required />
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required />
            <select name="supplier_id" required>
                <option value="">-- Select Supplier --</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $s['id'] == $product['supplier_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>

            <button type="submit">Update Product</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('editProductForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const form = new FormData(this);
        fetch(`/products/<?= $product['id'] ?>/update`, { method: 'POST', body: form })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // show message then redirect to product list
                    window.location.href = '/products';
                } else if (data.errors) {
                    // display field errors (simple example)
                    console.log('Errors', data.errors);
                } else {
                    alert(data.message || 'Update failed');
                }
            }).catch(err => console.error(err));
    });
</script>