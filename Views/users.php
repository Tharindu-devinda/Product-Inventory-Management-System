<div class="py-0 mx-2">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">All Users</h1>
        <a href="/users" class="bg-amber-400 hover:bg-amber-500 text-white font-bold px-6 py-2 rounded">
            + Create User
        </a>
    </div>

    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-amber-500 text-white">
            <tr>
                <th class="border border-gray-300 px-4 py-2">UNo</th>
                <th class="border border-gray-300 px-4 py-2">Username</th>
                <th class="border border-gray-300 px-4 py-2">Email</th>
                <th class="border border-gray-300 px-4 py-2">Role</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="hover:bg-orange-100">
                    <td class="border border-gray-300 px-4 py-2">
                        <?= $user['id'] ?>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <?= $user['username'] ?>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <?= $user['email'] ?>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <?= $user['role'] ?>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a href="/users/<?= $user['id'] ?>/edit" class="bg-blue-500 text-white px-3 py-1 rounded">Edit</a>
                        <button data-id="<?= $user['id'] ?>"
                            class="delete-btn bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- DELETE MODAL -->
    <div id="deleteModal" class="fixed inset-0 hidden items-center justify-center bg-black/25">
        <div class="bg-white p-5 rounded-lg shadow-md w-80">
            <h2 class="text-lg font-bold mb-3">Confirm Delete</h2>
            <p class="mb-4">Are you sure you want to delete this user?</p>

            <div class="flex justify-end gap-2">
                <button id="cancelBtn" class="px-3 py-1 bg-gray-300 rounded">Cancel</button>
                <button id="confirmDeleteBtn" class="px-3 py-1 bg-red-500 text-white rounded">Delete</button>
            </div>
        </div>
    </div>

    <script>
        let selectedUserId = null;
        const deleteModal = document.getElementById('deleteModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        // Open modal when Delete button clicked
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                selectedUserId = this.dataset.id;
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            });
        });

        // Close modal when Cancel clicked
        cancelBtn.addEventListener('click', function () {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        });

        // Confirm and execute delete
        confirmDeleteBtn.addEventListener('click', function () {
            fetch(`/users/${selectedUserId}/delete`, {
                method: 'POST'
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Hide modal
                        deleteModal.classList.add('hidden');
                        deleteModal.classList.remove('flex');

                        // Find and remove the row
                        const row = document.querySelector(`button[data-id="${selectedUserId}"]`).closest('tr');
                        if (row) {
                            row.style.opacity = '0';
                            row.style.transition = 'opacity 0.3s ease';
                            setTimeout(() => row.remove(), 300);
                        }

                        // Show success message
                        const successMsg = document.createElement('div');
                        successMsg.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg font-bold text-base';
                        successMsg.style.zIndex = '9999';  // Ensure it's on top
                        successMsg.textContent = '✓ User deleted successfully!';
                        document.body.appendChild(successMsg);

                        // Auto-remove message after 3 seconds
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
    </script>
</div>