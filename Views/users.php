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

    <script>
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                if (!confirm('Delete this user?')) return;
                const id = this.dataset.id;
                fetch(`/users/${id}/delete`, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            // remove row
                            const row = this.closest('tr');
                            if (row) row.remove();
                        } else {
                            alert(data.message || 'Delete failed');
                        }
                    })
                    .catch(() => alert('Server error'));
            });
        });
    </script>
</div>