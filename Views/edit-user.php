<!-- Edit User Form -->
<div class="py-5 mx-2">
    <div class="max-w-sm mx-auto mt-8 py-6 px-4 bg-white rounded-lg shadow-md">
        <section class="text-center mb-6">
            <h1 class="text-2xl font-bold">Edit User</h1>
        </section>

        <form id="editForm">
            <div class="form-group mt-1">
                <label for="username">User Name :</label>
                <input type="text" id="username" name="username" minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+"
                    value="<?= $user['username'] ?>" title="Only letters, numbers, underscore. Min 3 characters."
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Enter user name" required>
                <small class="text-red-500"></small>
            </div>

            <div class="form-group mt-1">
                <label for="email">Email Address :</label>
                <input type="email" id="email" name="email" value="<?= $user['email'] ?>"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="example@gmail.com" minlength="6" pattern=".{6,}" title="Minimum 6 characters required"
                    required>
                <small class="text-red-500"></small>
            </div>

            <div class="form-group mt-1">
                <label for="role">User Role :</label>
                <select id="role" name="role"
                    class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                    required>
                    <option value="">-- Select Role --</option>
                    <option value="super_admin" <?= $user['role'] === 'super_admin' ? 'selected' : '' ?>>Super Admin
                    </option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : '' ?>>Employee</option>
                </select>
                <small class="text-red-500"></small>
            </div>

            <button type="submit"
                class="btn bg-amber-500 hover:bg-amber-600 mt-4 text-white font-bold py-2 px-4 rounded w-full">
                Update User
            </button>
        </form>

        <a href="/users-list"
            class="block mt-2 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded w-full text-center">
            Back to Users
        </a>
    </div>
</div>

<script>
    document.getElementById("editForm").addEventListener("submit", function (e) {
        e.preventDefault();

        // Clear previous errors
        document.querySelectorAll('small.text-red-500').forEach(small => {
            small.textContent = '';
        });

        let formData = new FormData(this);
        let userId = <?= $user['id'] ?>;

        fetch(`/users/${userId}/update`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const successMsg = document.createElement('p');
                    successMsg.className = 'text-green-500 text-center font-bold';
                    successMsg.textContent = data.message;
                    document.querySelector('form').parentElement.insertBefore(successMsg, document.querySelector('form'));

                    setTimeout(() => {
                        window.location.href = '/users-list';
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