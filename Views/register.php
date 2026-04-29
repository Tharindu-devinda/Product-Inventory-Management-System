<div class="login flex justify-center min-h-screen pt-5 bg-orange-100">
    <div class="container mx-3">
        <p class="bg-amber-500 max-w-sm mx-auto text-center text-lg font-bold text-white p-2 rounded-lg">Product
            Inventory Management System</p>
        <main class="login-card max-w-sm mx-auto mt-2 py-2 px-4 bg-white rounded-lg shadow-md">

            <section class="brand text-center text-2xl font-bold">
                <h1>Create Account</h1>
            </section>

            <?php if (!empty($success)): ?>
                <p class="text-green-600 text-center"><?= $success ?></p>
            <?php endif; ?>

            <form id="registerForm">
                <div class="form-group mt-1">
                    <label for="username">User Name :</label> <br />
                    <input type="text" id="username" name="username" minlength="3" maxlength="20"
                        pattern="[a-zA-Z0-9_]+" value="<?= $old['username'] ?? '' ?>"
                        title="Only letters, numbers, underscore. Min 3 characters." class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2
                    focus:ring-amber-500" placeholder="Enter your user name" required>
                    <small class="text-red-500"> <?= $errors['username'] ?? '' ?> </small>
                </div>


                <div class="form-group mt-1">
                    <label for="email">Email Address :</label> <br />
                    <input type="email" id="email" name="email" value="<?= $old['email'] ?? '' ?>" class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2
                    focus:ring-amber-500" placeholder="example@gmail.com" autocomplete="email" minlength="6"
                        pattern=".{6,}" title="Minimum 6 characters required" required>
                    <small class="text-red-500"> <?= $errors['email'] ?? '' ?> </small>
                </div>

                <div class="form-group mt-1">
                    <label for="password">Password</label> <br />
                    <input type="password"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        id="password" name="password" placeholder="Enter your password" autocomplete="new-password"
                        minlength="6" pattern=".{6,}" title="Minimum 6 characters required" required>
                    <small class="text-red-500"> <?= $errors['password'] ?? '' ?> </small>
                </div>

                <div class="form-group mt-1">
                    <label for="confirm_password">Confirm Password</label> <br />
                    <input type="password"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        id="confirm_password" name="confirm_password" placeholder="Confirm your password"
                        autocomplete="new-password" required>
                    <small class="text-red-500"> <?= $errors['confirm_password'] ?? '' ?> </small>
                </div>

                <div class="form-group mt-1">
                    <label for="role">User Role :</label> <br />
                    <select id="role" name="role"
                        class="w-full mt-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500"
                        required>
                        <option value="">-- Select Role --</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="employee">Employee</option>
                    </select>
                    <small class="text-red-500"> <?= $errors['role'] ?? '' ?> </small>
                </div>

                <p id="error" style="color:red;"></p>

                <button class="btn bg-amber-500 hover:bg-amber-600 mt-3 text-white font-bold py-2 px-4 rounded w-full"
                    type="submit">Register</button>
            </form>
            <a href="/users-list"
                class="block mt-4 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded w-full text-center">
                Back to Users
            </a>
        </main>
    </div>
    <script>
        document.getElementById("registerForm").addEventListener("submit", function (e) {
            e.preventDefault();  // Prevent page reload

            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            let error = document.getElementById("error");

            // Clear previous errors before validation
            error.textContent = "";
            document.querySelectorAll('small.text-red-500').forEach(small => {
                small.textContent = '';
            });

            // Create FormData from the form
            let formData = new FormData(this);

            // Send AJAX request
            fetch("/users/store", {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // Clear previous errors first
                    document.querySelectorAll('small.text-red-500').forEach(small => {
                        small.textContent = '';
                    });

                    if (data.success) {
                        error.textContent = "";
                        const successMsg = document.createElement('p');
                        successMsg.className = 'text-green-600 text-center';
                        successMsg.textContent = data.message;
                        document.querySelector('form').parentElement.insertBefore(successMsg, document.querySelector('form'));
                        document.getElementById("registerForm").reset();  // Clear form

                        // Remove success message after 3 seconds
                        setTimeout(() => {
                            successMsg.remove();
                        }, 3000);  // 3000 milliseconds = 3 seconds
                    } else {
                        // Show validation errors
                        if (data.errors) {
                            console.log('Errors:', data.errors);  // Debug log
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
</div>